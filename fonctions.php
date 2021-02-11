<?php
include 'bd.php';

function getSentences($bdd)
{
    $req = $bdd->prepare("SELECT * FROM sentences");
    $req->execute();
    $sentences = $req->fetchAll();
    $req->closeCursor();
    return $sentences;
}

function getSentencesPrac($bdd)
{
    $req = $bdd->prepare("SELECT * FROM practice");
    $req->execute();
    $sentences = $req->fetchAll();
    $req->closeCursor();
    return $sentences;
}

function getLetters($bdd)
{
    $req = $bdd->prepare("SELECT letter FROM letters");
    $req->execute();
    $letters = $req->fetchAll();
    $req->closeCursor();
    return $letters;
}

function getPhrases($bdd)
{
    $req = $bdd->prepare("SELECT * FROM phrases");
    $req->execute();
    $sentences = $req->fetchAll();
    $req->closeCursor();
    return $sentences;
}

function getEntrainement($bdd)
{
    $req = $bdd->prepare("SELECT * FROM entrainement");
    $req->execute();
    $sentences = $req->fetchAll();
    $req->closeCursor();
    return $sentences;
}

function getSubject($bdd, $id)
{
    $req = $bdd->prepare("SELECT * FROM subjects WHERE subjects.id_subject = :id");
    $req->bindParam(':id', $id);
    $req->execute();
    $subject = $req->fetchAll();
    $req->closeCursor();
    return $subject[0];
}

function getBack_1($bdd, $id)
{
    $req = $bdd->prepare("SELECT * FROM 1_back WHERE id_1_back = :id");
    $req->bindParam(':id', $id);
    $req->execute();
    $trial = $req->fetchAll();
    $req->closeCursor();
    return $trial[0];
}

function getBack_2($bdd, $id)
{
    $req = $bdd->prepare("SELECT * FROM 2_back WHERE id_2_back BETWEEN :id AND :id + 1");
    $req->bindParam(':id', $id);
    $req->execute();
    $trial = $req->fetchAll();
    $req->closeCursor();
    return $trial;
}

function getBack_2_raw($bdd)
{
    $req = $bdd->prepare("SELECT * FROM 2_back");
    $req->execute();
    $trial = $req->fetchAll();
    $req->closeCursor();
    return $trial;
}

function getBack_2_subject($bdd, $subject)
{
    $req = $bdd->prepare("SELECT id_subject, id_2_back, AVG(correct) as target_correct FROM back_2 WHERE is_target = 1 AND id_subject = :id GROUP BY  id_subject, id_2_back;");
    $req->bindParam(':id', $subject);
    $req->execute();
    $trial = $req->fetchAll();
    $req->closeCursor();
    return $trial;
}

function getBack_2_subject_fa($bdd, $subject)
{
    $req = $bdd->prepare("SELECT id_subject, id_2_back, 1 - AVG(correct) as false_alarm FROM back_2 WHERE is_target = 0 AND id_subject = :id GROUP BY  id_subject, id_2_back;");
    $req->bindParam(':id', $subject);
    $req->execute();
    $trial = $req->fetchAll();
    $req->closeCursor();
    return $trial;
}

function upBack_1($bdd, $id)
{
    $req = $bdd->prepare("UPDATE subjects SET back_1_level = back_1_level + 1 WHERE id_subject = :id");
    $req->bindParam(':id', $id);
    $req->execute();
    $req->closeCursor();
}

function upBack_2($bdd, $id)
{
    $req = $bdd->prepare("UPDATE subjects SET back_2_level = back_2_level + 2 WHERE id_subject = :id");
    $req->bindParam(':id', $id);
    $req->execute();
    $req->closeCursor();
}

function getAllSubjects($bdd)
{
    $req = $bdd->prepare("SELECT * FROM subjects");
    $req->execute();
    $subject = $req->fetchAll();
    $req->closeCursor();
    return $subject;
}

function insertSubject($bdd)
{
    $req = $bdd->prepare("INSERT INTO subjects VALUES ()");
    $req->execute();
    $req->closeCursor();
}

function getAudio($bdd)
{
    $req = $bdd->prepare("SELECT * FROM duree");
    #$req = $bdd->prepare("SELECT * FROM duree LIMIT 3");
    $req->execute();
    $audio = $req->fetchAll();
    $req->closeCursor();
    return $audio;
}

function getBack2_results($bdd)
{
    $req = $bdd->prepare("SELECT id_subject, trial_id, id_2_back, is_target, correct, rt, time_elapsed FROM back_2 WHERE part = '2-back'");
    $req->execute();
    $audio = $req->fetchAll();
    $req->closeCursor();
    return $audio;
}

function getComprehension($bdd)
{
    $req = $bdd->prepare("SELECT * FROM comprehension");
    $req->execute();
    $comp = $req->fetchAll();
    $req->closeCursor();
    return $comp;
}

function insertRepComp($bdd, $sujet, $question, $reponse)
{
    $req = $bdd->prepare("INSERT INTO comp_reponses (id_sujet, id_question, reponse_sujet) VALUES (:s, :q, :r)");
    $req->bindParam(':s', $sujet);
    $req->bindParam(':q', $question);
    $req->bindParam(':r', $reponse);
    $req->execute();
    $req->closeCursor();
}

function getTimeout($bdd, $sujet)
{
    $req = $bdd->prepare("SELECT timeout_var FROM rspan_training WHERE subject_id = :s AND timeout_var is not null");
    $req->bindParam(':s', $sujet);
    $req->execute();
    $comp = $req->fetchAll();
    $req->closeCursor();
    return $comp;
}

function get2backData($bdd)
{
    $req = $bdd->prepare("select id_subject, id_2_back, part, trial_id, trial_index, time_elapsed, rt, correct, response_type from back_2 where part = '2-back'");
    $req->execute();
    $comp = $req->fetchAll();
    $req->closeCursor();
    return $comp;
}

function array_to_csv_download($array, $filename = "export.csv", $delimiter = ";")
{
    // open raw memory as file so no temp files needed, you might run out of memory though
    $f = fopen('php://memory', 'w');
    // loop over the input array
    foreach ($array as $line) {
        // generate csv lines from the inner arrays
        fputcsv($f, $line, $delimiter);
    }
    // reset the file pointer to the start of the file
    fseek($f, 0);
    // tell the browser it's going to be a csv file
    header('Content-Type: application/csv');
    // tell the browser we want to save it instead of displaying it
    header('Content-Disposition: attachment; filename="' . $filename . '";');
    // make php send the generated csv lines to the browser
    fpassthru($f);
}

function get_2_back_main($bdd)
{
    $req = $bdd->prepare("select * from 2_back_main");
    $req->execute();
    $comp = $req->fetchAll();
    $req->closeCursor();
    return $comp;
}

function getSentencesPre($bdd){
    $req = $bdd->prepare("select * from sentences where id_sentences < 40");
    $req->execute();
    $sentences = $req->fetchAll();
    $req->closeCursor();
    return $sentences;
}

function getSentencesPost($bdd){
    $req = $bdd->prepare("select * from sentences where id_sentences > 40");
    $req->execute();
    $sentences = $req->fetchAll();
    $req->closeCursor();
    return $sentences;
}

function insertRepSub($bdd, $sujet, $question, $reponse)
{
    $req = $bdd->prepare("INSERT INTO sub_reponses (id_sujet, id_question_sub, reponse_sub_sujet) VALUES (:s, :q, :r)");
    $req->bindParam(':s', $sujet);
    $req->bindParam(':q', $question);
    $req->bindParam(':r', $reponse);
    $req->execute();
    $req->closeCursor();
}

function getMotiv($bdd, $s){
    $req = $bdd->prepare("SELECT * FROM sub_reponses WHERE id_sujet = :s");
    $req->bindParam(':s', $s);
    $req->execute();
    $comp = $req->fetchAll();
    $req->closeCursor();
    return $comp;
}