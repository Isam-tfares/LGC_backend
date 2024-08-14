<?php
require_once('DB.php');
class Phase_projet
{
    public static function getAllReceptions($fromDate, $toDate)
    {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT
            Phase_projet.*,
            Phase.libelle AS PhaseLibelle,
            Projet.abr_projet AS ProjetAbr,
            Personnel1.Nom_personnel AS PersonnelNom,        -- Nom_personnel related to Phase_projet.IDPersonnel
            betontypes.labelle AS TypeBetonLibelle,
            Materiaux.labelle AS MateriauxLibelle,
            Personnel2.Nom_personnel AS SaisieParNom          -- Nom_personnel related to Phase_projet.saisiePar
            FROM
                Phase_projet
            INNER JOIN
                Phase ON Phase_projet.IDPhase = Phase.IDPhase
            INNER JOIN
                Projet ON Phase_projet.IDProjet = Projet.IDProjet
            INNER JOIN
                Personnel AS Personnel1 ON Phase_projet.IDPersonnel = Personnel1.IDPersonnel
            INNER JOIN
                betontypes ON Phase_projet.IDType_beton = betontypes.beton_type_id
            INNER JOIN
                Materiaux ON Phase_projet.IDMateriaux = Materiaux.materiaux_id
            INNER JOIN
                Personnel AS Personnel2 ON Phase_projet.saisiePar = Personnel2.IDPersonnel
            WHERE
                Phase_projet.saisiele BETWEEN " . $fromDate . " AND " . $toDate);
            $stmt->execute();
            $receptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $receptions = Database::encode_utf8($receptions);
            return $receptions;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Database error: " . $e->getMessage()]);
        }
    }
    public static function getAllPreReceptions($fromDate, $toDate)
    {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT
            Pre_reception.*,
            Phase.libelle AS PhaseLibelle,
            Client.abr_client,
            Projet.abr_projet ,
            Personnel1.Nom_personnel AS PersonnelNom,        -- Nom_personnel related to Pre_reception.IDPersonnel
            betontypes.labelle AS TypeBetonLibelle,
            Materiaux.labelle AS MateriauxLibelle,
            Personnel2.Nom_personnel AS SaisieParNom         -- Nom_personnel related to Pre_reception.saisiePar
            FROM
                Pre_reception
            INNER JOIN
                Phase ON Pre_reception.IDPhase = Phase.IDPhase
            INNER JOIN
                Projet ON Pre_reception.IDProjet = Projet.IDProjet
            INNER JOIN
                Client ON Projet.IDClient = Client.IDClient
            INNER JOIN
                Personnel AS Personnel1 ON Pre_reception.IDPersonnel = Personnel1.IDPersonnel
            INNER JOIN
                betontypes ON Pre_reception.IDType_beton = betontypes.beton_type_id
            INNER JOIN
                Materiaux ON Pre_reception.IDMateriaux = Materiaux.materiaux_id
            INNER JOIN
                Personnel AS Personnel2 ON Pre_reception.saisiePar = Personnel2.IDPersonnel
            WHERE
            Pre_reception.etat_confirmation=0 
            AND Pre_reception.saisiele BETWEEN " . $fromDate . " AND " . $toDate);

            $stmt->execute();
            $receptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $receptions = Database::encode_utf8($receptions);
            return $receptions;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Database error: " . $e->getMessage()]);
        }
    }
    public static function validateReception($user_id, $reception_id)
    {
        return 1;

        // $stmt->bindParam(':date_debut', $data['date_debut']);
        // $stmt->bindParam(':date_fin', $data['date_fin']);
        // $stmt->bindParam(':date_prevus', $data['date_prevus']);
    }
    public static function insertPreReception($data, $user_id)
    {
        try {
            $db = Database::getInstance()->getConnection();

            // Prepare the SQL statement with placeholders for all parameters
            $stmt = $db->prepare("INSERT INTO Pre_reception (
            IDPhase, IDProjet, nombre, IDType_beton, IDMateriaux, observation, 
            date_debut, date_fin, date_prevus, saisiePar, prelevement_par, 
            Compression, Traction, Lieux_ouvrage
        ) VALUES (
            :IDPhase, :IDProjet, :nombre, :IDType_beton, :IDMateriaux, :observation, 
            :date_debut, :date_fin, :date_prevus, :saisiePar, :prelevement_par, 
            :Compression, :Traction, :Lieux_ouvrage
        )");

            // Bind the parameters with the appropriate values
            $stmt->bindParam(':IDPhase', $data['IDPhase'], PDO::PARAM_INT);
            $stmt->bindParam(':IDProjet', $data['IDProjet'], PDO::PARAM_INT);
            $stmt->bindParam(':nombre', $data['nombre'], PDO::PARAM_INT);
            $stmt->bindParam(':IDType_beton', $data['IDType_beton'], PDO::PARAM_INT);
            $stmt->bindParam(':IDMateriaux', $data['IDMateriaux'], PDO::PARAM_INT);
            $stmt->bindParam(':observation', $data['observation'], PDO::PARAM_STR);
            $stmt->bindParam(':date_debut', $data['date_debut'], PDO::PARAM_INT);
            $stmt->bindParam(':date_fin', $data['date_fin'], PDO::PARAM_INT);
            $stmt->bindParam(':date_prevus', $data['date_prevus'], PDO::PARAM_INT);
            $stmt->bindParam(':saisiePar', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':prelevement_par', $data['prelevement_par'], PDO::PARAM_INT);
            $stmt->bindParam(':Compression', $data['Compression'], PDO::PARAM_BOOL);
            $stmt->bindParam(':Traction', $data['Traction'], PDO::PARAM_BOOL);
            $stmt->bindParam(':Lieux_ouvrage', $data['Lieux_ouvrage'], PDO::PARAM_STR);

            // Execute the statement
            $stmt->execute();

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Database error: " . $e->getMessage()]);
            return false;
        }
    }

    public static function get($reception_id)
    {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT
            Phase_projet.*,
            Phase.libelle AS PhaseLibelle,
            Projet.abr_projet AS ProjetAbr,
            Personnel1.Nom_personnel AS PersonnelNom,        -- Nom_personnel related to Phase_projet.IDPersonnel
            betontypes.labelle AS TypeBetonLibelle,
            Materiaux.labelle AS MateriauxLibelle,
            Personnel2.Nom_personnel AS SaisieParNom          -- Nom_personnel related to Phase_projet.saisiePar
            FROM
                Phase_projet
            INNER JOIN
                Phase ON Phase_projet.IDPhase = Phase.IDPhase
            INNER JOIN
                Projet ON Phase_projet.IDProjet = Projet.IDProjet
            INNER JOIN
                Personnel AS Personnel1 ON Phase_projet.IDPersonnel = Personnel1.IDPersonnel
            INNER JOIN
                betontypes ON Phase_projet.IDType_beton = betontypes.beton_type_id
            INNER JOIN
                Materiaux ON Phase_projet.IDMateriaux = Materiaux.materiaux_id
            INNER JOIN
                Personnel AS Personnel2 ON Phase_projet.saisiePar = Personnel2.IDPersonnel
            WHERE
                Phase_projet.IDPhase_projet = :reception_id;");

            $stmt->bindParam(':reception_id', $reception_id);
            $stmt->execute();
            $reception = $stmt->fetch(PDO::FETCH_ASSOC);
            $reception = Database::encode_utf8([$reception])[0];
            return $reception;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Database error: " . $e->getMessage()]);
        }
    }
    public static function getPreReception($reception_id)
    {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT
            Pre_reception.*,
            Phase.libelle AS PhaseLibelle,
            Projet.abr_projet AS ProjetAbr,
            Personnel1.Nom_personnel AS PersonnelNom,        -- Nom_personnel related to Pre_reception.IDPersonnel
            --betontypes.labelle AS TypeBetonLibelle,
            --Materiaux.labelle AS MateriauxLibelle,
            Personnel2.Nom_personnel AS SaisieParNom          -- Nom_personnel related to Pre_reception.saisiePar
            FROM
                Pre_reception
            INNER JOIN
                Phase ON Pre_reception.IDPhase = Phase.IDPhase
            INNER JOIN
                Projet ON Pre_reception.IDProjet = Projet.IDProjet
            INNER JOIN
                Personnel AS Personnel1 ON Pre_reception.IDPersonnel = Personnel1.IDPersonnel
            INNER JOIN
                betontypes ON Pre_reception.IDType_beton = betontypes.beton_type_id
            INNER JOIN
                Materiaux ON Pre_reception.IDMateriaux = Materiaux.materiaux_id
            INNER JOIN
                Personnel AS Personnel2 ON Pre_reception.saisiePar = Personnel2.IDPersonnel
            WHERE
                Pre_reception.IDPre_reception = :reception_id;");
            $stmt->bindParam(':reception_id', $reception_id);
            $stmt->execute();
            $reception = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $reception = Database::encode_utf8([$reception])[0];
            return $reception;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Database error: " . $e->getMessage()]);
        }
    }
    public static function getReception($reception_id)
    {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT
            Phase_projet.*,
            Phase.libelle AS PhaseLibelle,
            Projet.abr_projet AS ProjetAbr,
            Personnel1.Nom_personnel AS PersonnelNom,        -- Nom_personnel related to Phase_projet.IDPersonnel
            betontypes.labelle AS TypeBetonLibelle,
            Materiaux.labelle AS MateriauxLibelle,
            Personnel2.Nom_personnel AS SaisieParNom          -- Nom_personnel related to Phase_projet.saisiePar
            FROM
                Phase_projet
            INNER JOIN
                Phase ON Phase_projet.IDPhase = Phase.IDPhase
            INNER JOIN
                Projet ON Phase_projet.IDProjet = Projet.IDProjet
            INNER JOIN
                Personnel AS Personnel1 ON Phase_projet.IDPersonnel = Personnel1.IDPersonnel
            INNER JOIN
                betontypes ON Phase_projet.IDType_beton = betontypes.beton_type_id
            INNER JOIN
                Materiaux ON Phase_projet.IDMateriaux = Materiaux.materiaux_id
            INNER JOIN
                Personnel AS Personnel2 ON Phase_projet.saisiePar = Personnel2.IDPersonnel
            WHERE
                Phase_projet.IDPhase_projet = :reception_id;");
            $stmt->bindParam(':reception_id', $reception_id);
            $stmt->execute();
            $reception = $stmt->fetch(PDO::FETCH_ASSOC);
            $reception = Database::encode_utf8([$reception])[0];
            return $reception;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Database error: " . $e->getMessage()]);
        }
    }
}
