<?php namespace Temply_Account;

// Just prepearing to Captcha implementation, based on Neural Networks. Empty class
// It's gonna work with some coeffs
// In future this text model probably will be extended or changed
//
// 0. General Coefficient (GC)
// 0.0 - 1.0
// For now Suspicious Coefficient Detection Limit (SCDL) is 0.6+
// But in future this number also could be controlled by NN
// (e.g. in case of attack it could be automaticly decreased)
//
// 0.1. SCDL Control example
// * Captcha Exception Count (CEC)
//      For minute (10) | For hour (12) | For day (3)
// -> Captcha Exception Progressivity Coefficient (CEPC)
//      - For all logs:
//          To control SCDL
//      - By method:
//          To control MSC(ch. 2)
//
// It should be calculated by NN from other coeffs (for now: RIPCs, MSC, UUAC)
//
// 1. Request Intensivity
//      For minute | For hour | For day
// we work with coeffs, not with ints, so i'll use Request Intensivity Progressivity Coefficient (RIPC)
//      for 10 min | for 12 h | for 3d
// Also there's some categories to check:
//      By IP :: By User :: By Method
//
// 2. Method Sensivity Coefficient (MSC)
// 0.0 - 2.0
// Some method could be oftenly requested, some - not.
// This value is should be provided by humans 
// Default value - 1.
// Formula: GC = [GC Medium Calculation] * MSC
//
// 3. Unusual User Agent Coefficient (UUAC)
// * All Unique UAs Count (AUAC) from Logs
// * Same UAs Count (SUAC) from logs (70% coincidence)
// * Statistical Unique UA Coefficient (SUUAC) - I needed some time and data to get it
// Formula: UUAC = SUAC / AUAC * SUUAC
//
// 4. Same Request Coefficient (SRC)
// Controls same requests amount
// * Coincidence Value Coefficient (CVC)
// * Same Requests Amount (SRA)
// * MSC
// Formula: CVC * SRA * MSC
//
// FINAL: Request Captcha if NN Result is > SCDL - send captcha (or deny request in some cases)

class rqHeuristic {

    private $data = [];

    public function __construct($data)
    {
        $this->data = $data;
    }

}