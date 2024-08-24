<?php
require_once('DB.php');

class NoteFrais
{
    public static function getDemandes($user_id)
    {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM notes_de_frais WHERE IDPersonnel = :user_id and statut=1");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $notesFrais = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $notesFrais = Database::encode_utf8($notesFrais);

            if (!$notesFrais) {
                return []; // No records found
            }

            foreach ($notesFrais as &$note) {
                $stmt = $db->prepare("
                    SELECT af.*, tc.libelle 
                    FROM articles_frais af
                    JOIN types_charges tc ON af.IDType_charge = tc.IDType
                    WHERE af.IDNote = :IDNote
                ");
                $stmt->bindParam(':IDNote', $note['IDNote'], PDO::PARAM_INT);
                $stmt->execute();
                $articlesFrais = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $articlesFrais = Database::encode_utf8($articlesFrais);

                $note['articles'] = $articlesFrais;
            }

            return $notesFrais;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Database error: " . $e->getMessage()]);
        }
    }
    public static function addNote($data, $user_id)
    {
        try {
            $db = Database::getInstance()->getConnection();
            $db->beginTransaction();

            // 1. Insert into notes_de_frais
            $stmt = $db->prepare("
                INSERT INTO notes_de_frais (IDPersonnel, date_note, statut, montant_total, date_creation, date_mise_a_jour)
                VALUES (:IDPersonnel, :date_note, 1, :montant_total, SYSDATE, SYSDATE)
            ");

            $montant_total = array_sum(array_column($data, 'montant')); // Calculate total amount

            $stmt->bindParam(':IDPersonnel', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':date_note', $data[0]['date'], PDO::PARAM_STR);
            $stmt->bindParam(':montant_total', $montant_total, PDO::PARAM_STR);
            $stmt->execute();

            // 2. Retrieve the ID of the newly inserted note
            $stmt = $db->query("SELECT MAX(IDNote) AS IDNote FROM notes_de_frais");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $IDNote = $result['IDNote'];

            // 3. Insert articles_frais
            $stmt = $db->prepare("
                INSERT INTO articles_frais (IDNote, IDType_charge, montant, date_frais, observation, date_creation, date_mise_a_jour)
                VALUES (:IDNote, :IDType_charge, :montant, :date_frais, :observation, SYSDATE, SYSDATE)
            ");

            foreach ($data as $item) {
                $stmt->bindParam(':IDNote', $IDNote, PDO::PARAM_INT);
                $stmt->bindParam(':IDType_charge', $item['typecharge'], PDO::PARAM_INT);
                $stmt->bindParam(':montant', $item['montant'], PDO::PARAM_STR);
                $stmt->bindParam(':date_frais', $item['date'], PDO::PARAM_STR);
                $stmt->bindParam(':observation', $item['observation'], PDO::PARAM_STR);
                $stmt->execute();
            }

            $db->commit();
            return ["success" => true, "message" => "Note and articles added successfully."];
        } catch (PDOException $e) {
            $db->rollBack();
            http_response_code(500);
            return ["success" => false, "message" => "Database error: " . $e->getMessage()];
        }
    }
}
