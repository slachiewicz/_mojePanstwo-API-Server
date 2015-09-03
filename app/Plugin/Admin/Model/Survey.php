<?php

class Survey extends AppModel {

    public $useTable = 'surveys';

    public function getSummary($id = 0) {
        if(!$id) return false;

        App::import('model','DB');
        $DB = new DB();

        $results = array(
            'survey' => $DB->selectAssoc("
                SELECT
                  surveys.*
                FROM surveys
                WHERE
                  surveys.id = $id"),
            'questions' => $DB->selectAssocs("
                SELECT
                  surveys_questions.*
                FROM surveys_questions
                WHERE
                  surveys_questions.survey_id = $id")
        );

        foreach($results['questions'] as $i => $question) {
            $results['questions'][$i]['answers'] = $DB->selectAssocs("
                SELECT
                  surveys_answers.answer, COUNT(surveys_answers.id) as `count`
                FROM surveys_answers
                WHERE
                  surveys_answers.question_id = " . $question['id'] . "
                GROUP BY surveys_answers.answer
                ");
        }

        return $results;
    }

}