<?php

namespace App\Http\Controllers;
use App\Exports\ClearanceExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use ZipArchive;

class ReportController extends Controller
{

    private $_accessToken = 'WjlNxSP2URBXOcoDVhb3KRU6aBXmiJMMfb8xRnnn';
    private $_surveyID = 'SV_78tfiorSTVqiPrw';
    private $_progressID;
    private $_fileID;

    private $_currentSurveys;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {



        $this->requestExportDocument();
        $this->checkProgressForDocument();
        $this->parseExportDocument();


        $notArray = explode(',', $this->_currentSurveys);
        $entries = DB::table('entries')->whereNotIn('entryBeachID', $notArray)->get();


        $dataTimestamp = date('m_d_Y');

        return Excel::download(new ClearanceExport($entries), 'exit survey_export ' . $dataTimestamp .'.xlsx');

    }

    public function requestExportDocument()
    {
        $curlUrl = 'https://yul1.qualtrics.com/API/v3/surveys/' . $this->_surveyID .'/export-responses';

        $curlParams = json_encode(['format' => 'json']);
        $curl = curl_init();

        $header = array();
        $header[] = 'Content-type: application/json';
        $header[] = 'X-API-TOKEN: '.$this->_accessToken;


        curl_setopt($curl, CURLOPT_HTTPHEADER,$header);
        curl_setopt($curl, CURLOPT_POST,true);
        curl_setopt($curl, CURLOPT_POSTFIELDS,$curlParams);
        curl_setopt($curl, CURLOPT_URL, $curlUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $rest = json_decode(curl_exec($curl), true);

        $this->_progressID = $rest['result']['progressId'];
        curl_close($curl);



    }
    public function checkProgressForDocument()
    {
        sleep(5);

        $opts = array(
            'http' => array(
                'method' => "GET",
                'Content-type' => 'application/json',
                'header' => "X-API-TOKEN: " . $this->_accessToken . "\r\n"
            )
        );

        $context = stream_context_create($opts);

// Open the file using the HTTP headers set above
        $file = file_get_contents('https://yul1.qualtrics.com/API/v3/surveys/' . $this->_surveyID . '/export-responses/' . $this->_progressID, false, $context);

        $fileArray = json_decode($file, true);


        $this->_fileID = $fileArray['result']['fileId'];
    }

    public function parseExportDocument()
    {
        $opts = array(
            'http'=>array(
                'method'=>"GET",
                'header'=>"X-API-TOKEN: " . $this->_accessToken . "\r\n"
            )
        );

        $context = stream_context_create($opts);

        $file2 = file_get_contents('https://yul1.qualtrics.com/API/v3/surveys/' . $this->_surveyID .'/export-responses/' .  $this->_fileID.'/file', false, $context);

        $res = file_put_contents('gigs2.zip', $file2, FILE_TEXT);


        $zip = new ZipArchive;
        if ($zip->open('gigs2.zip') === TRUE) {
            $zip->extractTo('/Users/stevereed/Sites/localhost/clearancereports/public/uploads/');
            $zip->close();
            //    echo 'ok';
        } else {
            echo 'failed';
        }


        // Read the JSON file
        $json = file_get_contents('uploads/Exit Questionnaire - Production.json');

// Decode the JSON file
        $json_data = json_decode($json,true);

// Display data
        $surveyBeachIDs = [];
        if(!empty($json_data['responses']))
        {
            if(is_array($json_data['responses']))
            {
                foreach($json_data['responses'] as $clearPerson)
                {
                    $surveyBeachIDs[] = $clearPerson['values']['Username'];
                }
            }
        }

        $this->_currentSurveys = implode(',', $surveyBeachIDs);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
