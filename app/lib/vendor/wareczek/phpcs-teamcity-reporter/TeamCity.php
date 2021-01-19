<?php

namespace PHP_CodeSniffer\Reports;

use PHP_CodeSniffer\Files\File;

class TeamCity implements Report {

    public function generateFileReport($report, File $phpcsFile, $showSources = false, $width = 80) {
        if ($report['errors'] === 0 && $report['warnings'] === 0) {
            return false;
        }

        $file = str_replace(getcwd() . '/', '', $report['filename']);

        echo sprintf('##teamcity[testStarted name=\'%s\']', $file) . PHP_EOL;

        foreach ($report['messages'] as $line => $lineErrors) {
            foreach ($lineErrors as $column => $colErrors) {
                foreach ($colErrors as $error) {
                    echo sprintf(
                        '##teamcity[testFailed name=\'%s\' message=\'line %d, col %d, %s (%s) %s\']',
                        $file,
                        $line,
                        $column,
                        ($error['type'] === 'ERROR' ? 'Error' : 'Warning'),
                        $error['source'],
                        str_replace('\'', '|\'', $error['message'])
                    ) . PHP_EOL;
                }
            }
        }

        echo sprintf('##teamcity[testFinished name=\'%s\']', $file) . PHP_EOL;

        return true;
    }

    public function generate(
        $cachedData,
        $totalFiles,
        $totalErrors,
        $totalWarnings,
        $totalFixable,
        $showSources = false,
        $width = 80,
        $interactive = false,
        $toScreen = true
    ) {
        echo '##teamcity[testSuiteStarted name=\'phpcs\']' . PHP_EOL;
        echo $cachedData;
        echo '##teamcity[testSuiteFinished name=\'phpcs\']' . PHP_EOL;
    }

}
