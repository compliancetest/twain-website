<?php

namespace App;

class CA04 extends TestCaseValidationAbstract
{

    /**
     * Server validation for CA-04 test case
     */
    public function testCaseRules()
    {
        $logEntry = $this->transaction->logs()->where([
            'data_argument_type' => 'DAT_EXTIMAGEINFO',
            'data_group' => 'DG_IMAGE',
            'messages' => 'MSG_GET',
        ])->orderBy('execution_order')->first();

        $data = $logEntry->getOutput();

        /*
         * For the first DG_IMAGE / DAT_EXTIMAGEINFO / MSG_GET pExtImageInfo.Items[InfoID=TWEI_BARCODECOUNT].NumItems=0
         */
        $status = false;
        foreach ($data['pExtImageInfo']['Items'] as $item) {
            if ($item['InfoID'] == 'TWEI_BARCODECOUNT' && ($item['NumItems'] == 0 || ($item['NumItems'] == 1 && is_array($item['Item']) && in_array(0, $item['Item'])))) {
                $status = true;
            }
        }
        if (!$status) {
            $this->reasons[] = 'Barcode was detected although barcode detection was disabled.';
        }

        /**
         * Check that all supported barcode types in pCapability.hContainer.ItemList reported by DG_CONTROL / DAT_CAPABILITY / MSG_GET
         * where pCapability.Cap = ICAP_SUPPORTEDBARCODETYPES are mentioned in the following (except the first one DG_IMAGE / DAT_EXTIMAGEINFO / MSG_GET)
         * pExtImageInfo.Items[InfoID=TWEI_BARCODETYPE].Item
         */
        $logEntry = $this->transaction->logs()->where([
            'data_argument_type' => 'DAT_CAPABILITY',
            'data_group' => 'DG_CONTROL',
            'messages' => 'MSG_GET',
            'session_state' => 4,
        ])->orderBy('execution_order')->first();

        $data = $logEntry->getOutput();

        $logs = $this->transaction->logs()->where([
            'data_argument_type' => 'DAT_EXTIMAGEINFO',
            'data_group' => 'DG_IMAGE',
            'messages' => 'MSG_GET',
        ])->orderBy('execution_order')->get();
        $supportedCaps = array_flip($data['pCapability']['hContainer']['ItemList']);

        foreach ($logs as $k => $log) {
            if ($k == 0) continue;
            $logData = $log->getOutput();
            foreach ($logData['pExtImageInfo']['Items'] as $item) {
                if ($item['InfoID'] == 'TWEI_BARCODETYPE') {
                    $key = array_shift($item['Item']);
                    if (isset($supportedCaps[$key])) {
                        unset($supportedCaps[$key]);
                    }
                }
            }
        }
        if (!empty($supportedCaps)) {
            foreach($supportedCaps as $capName => $v) {
                $this->reasons[] = sprintf('Barcode type "%s" was not detected, although was reported as supported.', $capName);
            }
        }
    }

    public function getFilesNumber()
    {
        return 0;
    }
}