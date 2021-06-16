<?php

class generateCSVs {
    private $fileName;

    private $write;

    public function __construct() {
		$this->fileName = 'test.csv';
		if(!empty(getopt("f:")['f'])) {
			$this->fileName = getopt("f:")['f'];
		}
    }

    public function __destruct() {
        if (null !== $this->write) {
            fclose($this->write);
        }
    }

    public function generate() {
        if (file_exists($this->fileName)) {
            throw new \Exception(
                sprintf(
                    'File already exists, please delete \'%s\' file and try again!',
                    $this->fileName
                )
            );
        }

        $this->exportCSV($this->fileName, '"Month name","salary date","bonus date"');
        foreach ($this->getMonths() as $month) {
            $baseSalaryDay = $this->getBaseDay($month);
            $bonusSalaryDay = $this->getBonusDay($month);
            $this->exportCSV(
                $this->fileName,
                sprintf(
                    '"%s","%s","%s"',
                    date('F', $baseSalaryDay),
                    date('d-m-Y', $baseSalaryDay),
                    date('d-m-Y', $bonusSalaryDay)
				)
			);
		}
        return $this->fileName;
    }

    private function getMonths($from = null) {
		$months = array();
        if (is_null($from)) {
            $from = time();
        }

        for ($month = date('n', $from); $month<=12; $month++) {
           $months[] = $month;
        }
		return $months;
    }
	
    private function exportCSV($writeFile, $line) {
        if (is_null($this->write)) {
            $this->write = fopen($writeFile, 'w');
        }

        if (!(fwrite($this->write, $line . "\n"))) {
            throw new \Exception(
                sprintf(
                    'Error: %s',
                    $writeFile
                )
            );
        }
    }
	
    private function isWeekend($dateTime) {
        $day = date('N', $dateTime);
        if ($day > 5) {
            return true;
        }
        return false;
    }

    private function getBaseDay($month) {
        for ($day = 0; $day < 3; $day++) {
            $lastDay = mktime(1, 1, 1, $month+1, 0-$day);

            if (!($this->isWeekend($lastDay))) {
                break;
            }
        }
        return $lastDay;
    }

    private function getBonusDay($month, $day = 15) {
        $lastDay = mktime(1, 1, 1, $month, $day);
        if ((!($this->isWeekend($lastDay)))) {
            return $lastDay;
        }
        return strtotime('next wednesday', $lastDay);
    }
}