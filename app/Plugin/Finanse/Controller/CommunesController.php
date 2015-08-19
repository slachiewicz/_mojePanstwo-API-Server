<?php

/**
 * @property CommuneBudget CommuneBudget
 */
class CommunesController extends AppController {

    public $uses = array('Finanse.CommuneBudget');

    private static $rangeQuery = 'zakres';
    private static $rangeTypes = array('q', 'h');
    private static $rangeDefaultYear = 2014;

    public function sections($communeId, $type) {
        $range = $this->getRange();
        $sections = $this->CommuneBudget->getSections($communeId, $type, $range);

        $this->set(array(
            'sections' => $sections,
            '_serialize' => array('sections')
        ));
    }

    public function section($id, $communeId, $type) {
        $range = $this->getRange();
        $section = $this->CommuneBudget->getSection($id, $communeId, $type, $range);

        $this->set(array(
            'section' => $section,
            '_serialize' => array('section')
        ));
    }

    private function getRange() {
        $quarters = array();
        $year = self::$rangeDefaultYear;

        if(isset($this->request->query[self::$rangeQuery])) {
            $range = $this->request->query[self::$rangeQuery];
            if(strlen($range) == 4 && is_numeric($range)) {
                $year = (int) $range;
            } elseif(strlen($range) == 6) {
                $year = (int) substr($range, 0, 4);
                $type = strtolower(substr($range, 4, 1));

                if(in_array($type, self::$rangeTypes))
                {
                    $num = (int) substr($range, 5, 1);
                    if($type == 'h')
                        $num *= 2;

                    for ($q = 1; $q <= (($num > 4) ? 4 : $num); $q++)
                        $quarters[] = $q;
                }
            }
        }

        return array(
            'year' => $year,
            'quarters' => $quarters
        );
    }

}