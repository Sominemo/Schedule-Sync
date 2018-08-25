<?php
/**
 * Report
 *
 * Writing reports
 *
 * @package Temply-Account\Services
 * @license GPL-2.0
 * @author Sergey Dilong
 */
/**
 * Report class
 *
 * Saving logs to DB
 *
 * @package Temply-Account\Services
 * @license GPL-2.0
 * @author Sergey Dilong
 */
class Report
{
    /** @var string $report_table DB table for logs */
    private $report_table = "api_report";
    /** @var bool|array $id Report DB ID */
    private $id = false;
    /** @var bool|array $data Data to be saved */
    private $data = false;

    /**
     * New report
     *
     * Write logs to DB
     *
     * @api
     * @param array $data Data to save
     * @return bool If `true` - success
     * @throws apiException
     * * [104] Failed to write log
     */
    public function __construct($data = [])
    {
        global $pdo, $global_report_data, $the_return_stream;

        // If connection to DB error - terminate
        if (DB_CONNECTION_SUCCESS !== true) {
            return false;
        }

        // Stop timers, get current user and output
        $global_report_data['result'] = (count($the_return_stream) > 0 ? $the_return_stream : $data);
        $global_report_data['time'] = microtime(true) - $global_report_data['time'];
        $global_report_data['user_id'] = (Auth::User() ? Auth::User()->get()['id'] : 0);

        // Save data
        $this->data = $global_report_data;
        $v = db::values($global_report_data);

        // Write data
        $r = $pdo->prepare("INSERT into `{$this->report_table}` SET $v");
        $r->execute($global_report_data);
        $m = intval($pdo->lastInsertId());

        // Report writing error
        if (!$m > 0) {throw new apiException(104);return false;}
        $this->id = $m;

        return true;
    }

    /**
     * Get data
     *
     * Get report data
     *
     * @api
     * @return array|bool Saved data. If `false` - no data
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Get Report ID
     *
     * Unique ID of just saved report
     *
     * @api
     * @return int|bool Report ID. If `false` - Report error
     */
    public function getID()
    {
        return $this->id;
    }
}
