<?php

/**
 * @property Survey Survey
 * @property SurveyQuestion SurveyQuestion
 * @property SurveyAnswer SurveyAnswer
 */
class SurveyController extends AppController {

    public $uses = array('Survey.Survey', 'Survey.SurveyQuestion', 'Survey.SurveyAnswer');

    public function save()
    {
        try {

            if(!isset($this->request->data['survey']))
                throw new BadRequestException;

            $response = false;

            foreach($this->request->data['survey'] as $name => $questions)
            {
                $survey = $this->Survey->find('first', array(
                    'conditions' => array(
                        'Survey.name' => $name
                    )
                ));

                if(!$survey)
                    throw new BadRequestException;

                $answersCount = $this->SurveyAnswer->find('count', array(
                    'conditions' => array(
                        'SurveyAnswer.survey_id'    => $survey['Survey']['id'],
                        'SurveyAnswer.user_id'      => $this->Auth->user('id'),
                    )
                ));

                if($answersCount)
                    throw new Exception('Already voted');

                foreach($questions as $question => $answer)
                {
                    $surveyQuestion = $this->SurveyQuestion->find('first', array(
                        'conditions' => array(
                            'SurveyQuestion.survey_id' => $survey['Survey']['id'],
                            'SurveyQuestion.question' => $question
                        )
                    ));

                    if(!$surveyQuestion) {
                        $this->SurveyQuestion->clear();
                        $surveyQuestion = $this->SurveyQuestion->save(array(
                            'SurveyQuestion' => array(
                                'survey_id' => $survey['Survey']['id'],
                                'question' => $question
                            )
                        ));
                    }

                    $answers = array();
                    if(!is_array($answer))
                        $answers[] = $answer;
                    else
                        $answers = $answer;

                    foreach($answers as $answerStr) {
                        $this->SurveyAnswer->clear();
                        $surveyAnswer = $this->SurveyAnswer->save(array(
                            'SurveyAnswer' => array(
                                'survey_id'     => $survey['Survey']['id'],
                                'question_id'   => $surveyQuestion['SurveyQuestion']['id'],
                                'user_id'       => $this->Auth->user('id'),
                                'answer'        => $answerStr
                            )
                        ));
                    }
                }

                $response = true;
            }

            $this->set('response', $response);
            $this->set('_serialize', 'response');
        } catch (Exception $e) {
            $this->set('error', $e->getMessage());
            $this->set('_serialize', 'error');
        }
    }

}