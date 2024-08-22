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
            Projet.abr_projet,
            Personnel1.Nom_personnel AS PersonnelNom,        -- Nom_personnel related to Phase_projet.IDPersonnel
            betontypes.labelle AS TypeBetonLibelle,
            Materiaux.labelle AS MateriauxLibelle,
            Personnel2.Nom_personnel AS SaisieParNom,          -- Nom_personnel related to Phase_projet.saisiePar
            Client.abr_client,
            PV.image_path AS PVPath
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
            INNER JOIN
                Client ON Projet.IDClient = Client.IDClient
            LEFT JOIN
                PV ON Phase_projet.intervention_id = PV.intervention_id
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
            Personnel2.Nom_personnel AS SaisieParNom,         -- Nom_personnel related to Pre_reception.saisiePar
            PV.image_path AS PVPath
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
            LEFT JOIN
                PV ON Pre_reception.IDPre_reception = PV.IDPre_reception
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

    public static function validateReception($user_id, $IDPre_reception)
    {
        try {
            $db = Database::getInstance()->getConnection();

            // Begin the transaction
            $db->beginTransaction();

            // Fetch the pre-reception data
            $stmt = $db->prepare("SELECT * FROM Pre_reception WHERE IDPre_reception=:IDPre_reception and etat_confirmation=0");
            $stmt->bindParam(':IDPre_reception', $IDPre_reception, PDO::PARAM_INT);
            $stmt->execute();
            $preReception = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($preReception) {
                // Update the Pre_reception record
                $stmt = $db->prepare("UPDATE Pre_reception SET Modifie_par=:user_id, Modifie_le=SYSDATE, etat_confirmation=1 WHERE IDPre_reception=:IDPre_reception");
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->bindParam(':IDPre_reception', $IDPre_reception, PDO::PARAM_INT);
                $stmt->execute();

                // Insert into Phase_projet
                $stmt = $db->prepare("INSERT INTO Phase_projet (
                intervention_id, IDPhase, IDProjet, nombre, IDPersonnel, IDType_beton, IDMateriaux, Observation, saisiePar, prelevement_par, 
                Compression, Traction, Lieux_ouvrage, Traction_fend, saisiele, date_prevus, Modifie_le, Modifie_par
            ) VALUES (
                :intervention_id, :IDPhase, :IDProjet, :nombre, :IDPersonnel, :IDType_beton, :IDMateriaux, :Observation,
                :saisiePar, :prelevement_par,
                :Compression, :Traction, :Lieux_ouvrage, :Traction_fend, :saisiele, :date_prevus, SYSDATE, :modifie_par
            )");

                // Bind parameters
                $stmt->bindValue(':intervention_id', $preReception['intervention_id'] ?? null, PDO::PARAM_INT);
                $stmt->bindValue(':IDPhase', $preReception['IDPhase'] ?? null, PDO::PARAM_INT);
                $stmt->bindValue(':IDProjet', $preReception['IDProjet'] ?? null, PDO::PARAM_INT);
                $stmt->bindValue(':nombre', $preReception['nombre'] ?? null, PDO::PARAM_INT);
                $stmt->bindValue(':IDPersonnel', $preReception['IDPersonnel'] ?? null, PDO::PARAM_INT);
                $stmt->bindValue(':IDType_beton', $preReception['IDType_beton'] ?? null, PDO::PARAM_INT);
                $stmt->bindValue(':IDMateriaux', $preReception['IDMateriaux'] ?? null, PDO::PARAM_INT);
                $stmt->bindValue(':Observation', $preReception['Observation'] ?? null, PDO::PARAM_STR);
                $stmt->bindValue(':saisiePar', $preReception['saisiePar'] ?? null, PDO::PARAM_INT);
                $stmt->bindValue(':prelevement_par', $preReception['prelevement_par'] ?? null, PDO::PARAM_INT);
                $stmt->bindValue(':Compression', $preReception['Compression'] ?? null, PDO::PARAM_STR);
                $stmt->bindValue(':Traction', $preReception['Traction'] ?? null, PDO::PARAM_STR);
                $stmt->bindValue(':Lieux_ouvrage', $preReception['Lieux_ouvrage'] ?? null, PDO::PARAM_STR);
                $stmt->bindValue(':Traction_fend', $preReception['Traction_fend'] ?? null, PDO::PARAM_STR);
                $stmt->bindValue(':saisiele', $preReception['saisiele'] ?? null, PDO::PARAM_STR);
                $stmt->bindValue(':date_prevus', $preReception['date_prevus'] ?? null, PDO::PARAM_STR);
                $stmt->bindValue(':modifie_par', $user_id, PDO::PARAM_INT);

                // Execute the insert statement
                $stmt->execute();

                // Update reception status of intervention
                $stmt = $db->prepare("UPDATE interventions 
                SET etat_reception=1,
                date_modification=SYSDATE,
                modifie_par=:user_id
                WHERE intervention_id=:intervention_id");
                $stmt->bindParam(':intervention_id', $preReception['intervention_id']);
                $stmt->bindParam(":user_id", $user_id);
                $stmt->execute();

                // Commit the transaction
                $db->commit();
                if ($stmt->rowCount() > 0) {
                    return 1;
                } else {
                    return 0;
                }
            } else {
                // Rollback the transaction if pre-reception data is not found
                $db->rollBack();
                return ["success" => false, "message" => "Pre-reception not found."];
            }
        } catch (PDOException $e) {
            // Rollback the transaction in case of an error
            $db->rollBack();
            http_response_code(500);
            return ["success" => false, "message" => "Database error: " . $e->getMessage()];
        }
    }


    public static function insertPreReception($data, $user_id)
    {
        try {
            $db = Database::getInstance()->getConnection();
            // Prepare the SQL statement with placeholders for all parameters
            $stmt = $db->prepare("INSERT INTO Pre_reception (
            intervention_id,IDPhase, IDProjet, nombre,IDPersonnel, IDType_beton, IDMateriaux, Observation, saisiePar, prelevement_par, 
            Compression, Traction, Lieux_ouvrage,Traction_fend,saisiele,date_prevus
        ) VALUES (
            :intervention_id,:IDPhase, :IDProjet, :nombre,:IDPersonnel, :IDType_beton, :IDMateriaux, :observation,
            :saisiePar, :prelevement_par,
            :Compression, :Traction, :Lieux_ouvrage,:Traction_fend,SYSDATE," . $data["date_prevus"] . "
        )");
            // Bind all parameters to their values
            $stmt->bindParam(':intervention_id', $data['intervention_id']);
            $stmt->bindParam(':IDPhase', $data['IDPhase']);
            $stmt->bindParam(':IDProjet', $data['IDProjet']);
            $stmt->bindParam(':nombre', $data['nombre']);
            $stmt->bindParam(':IDPersonnel', $user_id);
            $stmt->bindParam(':IDType_beton', $data['IDType_beton']);
            $stmt->bindParam(':IDMateriaux', $data['IDMateriaux']);
            $stmt->bindParam(':observation', $data['observation']);
            $stmt->bindParam(':saisiePar', $user_id);
            $stmt->bindParam(':prelevement_par', $data['prelevement_par']);
            $stmt->bindParam(':Compression', $data['Compression']);
            $stmt->bindParam(':Traction', $data['Traction']);
            $stmt->bindParam(':Lieux_ouvrage', $data['Lieux_ouvrage']);
            $stmt->bindParam(':Traction_fend', $data['Traction_fend']);
            // Execute the statement
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                InterventionController::updateInterventionState($data['intervention_id'], $user_id);
            }
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
            Projet.abr_projet,
            Personnel1.Nom_personnel AS PersonnelNom,        -- Nom_personnel related to Phase_projet.IDPersonnel
            betontypes.labelle AS TypeBetonLibelle,
            Materiaux.labelle AS MateriauxLibelle,
            Personnel2.Nom_personnel AS SaisieParNom,          -- Nom_personnel related to Phase_projet.saisiePar
            Client.abr_client,
            PV.image_path AS PVPath
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
            INNER JOIN
                Client ON Projet.IDClient = Client.IDClient
            LEFT JOIN
                PV ON Phase_projet.IDPhase_projet = PV.IDPhase_projet
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
    public static function getPreReceptionByIntervention($intervention_id)
    {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT
            Pre_reception.*,
            Phase.libelle AS PhaseLibelle,
            Projet.abr_projet,
            Personnel1.Nom_personnel AS PersonnelNom,        -- Nom_personnel related to Pre_reception.IDPersonnel
            betontypes.labelle AS TypeBetonLibelle,
            Materiaux.labelle AS MateriauxLibelle,
            Personnel2.Nom_personnel AS SaisieParNom,          -- Nom_personnel related to Pre_reception.saisiePar
            Client.abr_client,
            PV.image_path AS PVPath
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
            INNER JOIN
                Client ON Projet.IDClient = Client.IDClient
            LEFT JOIN
                PV ON Pre_reception.IDPre_reception = PV.IDPre_reception
            WHERE
                Pre_reception.intervention_id = :intervention_id;");
            $stmt->bindParam(':intervention_id', $intervention_id);
            $stmt->execute();
            $reception = $stmt->fetch(PDO::FETCH_ASSOC);
            $reception = Database::encode_utf8([$reception])[0];
            return $reception;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Database error: " . $e->getMessage()]);
        }
    }
    public static function getReceptionByIntervention($intervention_id)
    {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT
            Phase_projet.*,
            Phase.libelle AS PhaseLibelle,
            Projet.abr_projet ,
            Personnel1.Nom_personnel AS PersonnelNom,        -- Nom_personnel related to Phase_projet.IDPersonnel
            betontypes.labelle AS TypeBetonLibelle,
            Materiaux.labelle AS MateriauxLibelle,
            Personnel2.Nom_personnel AS SaisieParNom,          -- Nom_personnel related to Phase_projet.saisiePar
            Client.abr_client,
            PV.image_path AS PVPath
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
            INNER JOIN
                Client ON Projet.IDClient = Client.IDClient
            LEFT JOIN
                PV ON PV.intervention_id = Phase_projet.intervention_id
            WHERE
                Phase_projet.intervention_id = :intervention_id;");
            $stmt->bindParam(':intervention_id', $intervention_id);
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
