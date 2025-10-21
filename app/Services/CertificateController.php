<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Image;
use Illuminate\Http\Request;
 
class CertificateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
	 ini_set('max_execution_time', 30000); // 300 seconds = 5 minutes
  
	    
// Path to the uploaded CSV file
$csvFile = public_path('allcertificates/Solid work cam 7.csv'); //  
$folderPath = public_path('allcertificates'); // Path to the folder containing your files

// Scan the folder and retrieve all files
$files = scandir($folderPath);

// Filter out `.` and `..` from the results
$files = array_diff($files, ['.', '..']);
foreach ($files as $file) {
    $csvFile = $folderPath . '/' . $file;
	echo $csvFile.'<br/>';
	 if (is_file($csvFile)) {
	if (($handle = fopen($csvFile, "r")) !== false) {
		// Skip the header row if your file has one
		$header = fgetcsv($handle); // Adjust if there's no header row
		$rowIndex = 0;
		// Loop through each row in the CSV
		while (($data = fgetcsv($handle)) !== false) {
			$rowIndex++;//added to index 1 for each one
			$studentName = $data[2]; // Column B//as before
			$courseName = $data[3]; // Column C become f
			$totalHours = $data[4]; // Column D - G
			$startDate = $data[5]; // Column E -H
			$endDate = $data[6]; // Column F - I
			$issueDate = $data[7]; // Column G letterd K 7 H
			$issueDaten = $data[8]; // Column H num J
			$studentNamear = $data[1]; // Column A //as before become b mean 1
			$courseNamear = $data[10]; // Column J -M

			// Output the data (for testing purposes)
			echo "Student Name (English): $studentName\n";
			echo "Course Name (English): $courseName\n";
			echo "Total Hours: $totalHours\n";
			echo "Start Date: $startDate\n";
			echo "End Date: $endDate\n";
			echo "Issue Date: $issueDate\n";
			echo "Student Name (Arabic): $studentNamear\n";
			echo "Course Name (Arabic): $courseNamear\n";
			echo "-----------------------\n";
			$no = time() . random_int(100, 999);
			$studentName = str_replace(['/','\\'],'.',$studentName);
			$studentNamear = str_replace(['/','\\'],'.',$studentNamear);
			$courseName = str_replace(['/','\\'],'_',$courseName);
			
			$parsedDate = \DateTime::createFromFormat('d/m/Y', $issueDate);
				if ($parsedDate) {
				   $issueDate =$parsedDate->format('d F Y');  
				}
			$fileToSave = str_replace('.csv','',$file);	 
			$this->certificate($studentName,$courseName,$totalHours,$startDate,$endDate, $issueDate,$no ,$fileToSave);
			
			$no = 'Y'.date('y').'/'.date('m').'/'.$rowIndex;
			$issueDaten = explode('/', $issueDaten); // Split the date into parts
			
			$year = @$issueDaten[0];
			if(strlen($year) == 2){
				$year = '20'.$year;
			}
			@$issueDaten = @$issueDaten[2] . '/' . @$issueDaten[1] . '/' .@$year ;
			if(is_string($issueDaten[1]) or is_string($issueDaten[2])){
				//$issueDaten = $data[7];
			   //$issueDaten = \DateTime::createFromFormat('d/m/Y', $issueDaten)->format('Y/m/d');
			   

			}

		   $this->experience($studentNamear,$courseNamear,$totalHours,$startDate,$endDate, '2025/09/15',$courseName,$no,$fileToSave );
			
			// Perform further processing or saving of data as needed
		}

		// Close the file
		fclose($handle);
	} else {
		echo "Failed to open the CSV file.";
	}
  }//endiffile 

}//end foreach
	    die();
     
    }
 
function certificate($studentName,$courseName,$totalHours,$startDate,$endDate, $issueDate,$no,$fileToSave){
	 require_once storage_path('app/vendor/autoload.php'); // Correct path to mPDF autoload.php

// Use the mPDF namespace
$fontDirs = [
    storage_path('app/fonts/agrandir'), // Path where your fonts are stored
];

// Font data
$fontData = [
    'agrandir' => [
        'R' => 'Agrandir-Regular.ttf',   // Regular
        'B' => 'Agrandir-TextBold.ttf', // Bold
    ],
];
$mpdf = new \Mpdf\Mpdf([
      'format' => [677, 520], // Custom width (300mm) and height (297mm)
    'orientation' => 'p', // 'P' for portrait, 'L' for landscape
    'imageDpi' => 30000, // Set DPI for better quality
	'fontDir' => $fontDirs,
    'fontdata' => $fontData,
    'default_font' => 'agrandir', // Set default font
]);
 
 $mpdf->useSubstitutions = true;
// Path to the certificate image
$certificateImage = public_path('images/certificate-template.jpg'); // Adjust to your image path

// Dynamic Data for Certificate
//$studentName = "Alaa Arrabi"; // Replace with dynamic student name
//$courseName = "PHP Web Development"; // Replace with dynamic course name
//$totalHours = "30"; // Total hours
//$startDate = "11/22/2033"; // Start date
//$endDate = "30/4/2033"; // End date
//$issueDate = "20 December 2033"; // Issue date
//$studentName = 'Mohammed Saleem Alzubaidi';
$html = '
<style>
body{
  background: url(' . $certificateImage . ') ; 
 width: 100%; 
      background-size: contain;
	  background-repeat: no-repeat;
}    
</style>
<div class="certificate-table">
    <table style="width:100%;" >
        <tr>
            <td colspan="3" class="student-name" style="padding-left:30px;padding-top:568px;font-weight:bold;font-size: 68px; text-align:center ;color:#538135;    text-shadow: 0px 1px 3px rgba(0, 0, 0, 0.5);  ">
                ' . $studentName . '
            </td>
        </tr>
        <tr>
            <td colspan="3" class="course-name" style="text-align:center;padding-left:30px;padding-top:138px;font-size: 50px;font-weight:bold;color:#538135;  text-shadow: 0px 1px 3px rgba(0, 0, 0, 0.5) ">
                ' . $courseName . '
            </td>
        </tr>
        <tr>
            <td   style="padding-left:400px;padding-top:341px;text-align: right;font-weight: bold;font-size: 30px;color:#538135;text-shadow: -2px 1px 12px rgba(0,0,0,0.10);font-family: arial">
             ' . $totalHours . '</td>  
			 <td style="padding-left:250px;padding-top:341px;text-align: right;font-weight: bold;font-size: 30px ;color:#538135;text-shadow: -2px 1px 12px rgba(0,0,0,0.10);font-family: arial">' . $startDate . '</td>  
			 <td style="padding-right:690px;padding-top:341px;text-align: right;font-weight: bold;font-size: 30px ;color:#538135;text-shadow: -2px 1px 12px rgba(0,0,0,0.10);font-family: arial">' . $endDate . '</td>
            
        </tr>
        <tr>
		   <td  style="text-align: right; padding-left: 535px;padding-top:432px;font-size: 26px;color:#538135;text-shadow: -1px 1px 1px rgba(0,0,0,0.02)">
                  <b>' . $no . '</b>
            </td>
			<td></td>
            <td  style="text-align: right; padding-right: 408px;padding-top:426px;font-size: 26px;color:#538135;text-shadow: -1px 1px 1px rgba(0,0,0,0.02)">
                  <b>' . $issueDate . '</b>
            </td>
        </tr>
    </table>
</div>
';
 
// Write HTML to PDF
$mpdf->WriteHTML($html);
$savePath = public_path('certificates/'.$fileToSave.'/'); // Adjust this path as needed

// Ensure the folder exists, create it if it doesn't
if (!file_exists($savePath)) {
    mkdir($savePath, 0777, true); // Create the folder with proper permissions
}
// Define the PDF file name
$fileName = 'certificate_' . $studentName.'_'.uniqid() . '.pdf'; // Dynamic file name to avoid overwriting
// Full path for the PDF file
$filePath = $savePath . $fileName;
// Save the PDF to the folder \Mpdf\Output\Destination::FILE
$mpdf->Output($filePath, \Mpdf\Output\Destination::FILE);

}
 public function experience($studentName,$courseNamear,$totalHours,$startDate,$endDate, $issueDate,$courseName,$no,$fileToSave ){
 
	 require_once storage_path('app/vendor/autoload.php'); // Correct path to mPDF autoload.php

 
$mpdf = new \Mpdf\Mpdf([
      'format' => [377, 500], // Custom width (300mm) and height (297mm)
    'orientation' => 'p', // 'P' for portrait, 'L' for landscape
    'imageDpi' => 30000, // Set DPI for better quality
	 
]);
 
 
// Path to the certificate image
$header = public_path('images/header.png'); 
$blue = public_path('images/bluelogosig.png'); 
$footer = public_path('images/footer.png'); 
 
$startDate = explode('/', $startDate); // Split the date into parts
@$startDate = @$startDate[2] . '/' . @$startDate[1] . '/' . @$startDate[0];
$endDate = explode('/', $endDate); // Split the date into parts
$endDate = @$endDate[2] . '/' . @$endDate[1] . '/' . @$endDate[0];
 
$html = '
  <style>
  body{
     font-family: "verdana"; 
           
  }
  </style>
<div class="certificate-table">
    <table style="width:100%;" >
        <tr>
            <td colspan="3" class="student-name" style=" padding-top:5px; ">
               <img src="' . $header . '"/>
            </td>
        </tr>
		<tr>
		  <td colspan="3" style="font-size: 20px;direction: rtl;">
		رقم الكتاب:- '.$no.'
		<br/>
تاريخ الكتاب :- '.$issueDate.'



		  </td>
		</tr>
		 
        <tr>
            <td colspan="3" class="course-name" style="text-align:center  ;padding-top:150px;font-size: 24px;font-weight:normal;color:#00;text-decoration:underline ">
                شهادة خبرة
            </td>
        </tr>
		<tr>
            <td colspan="3"  style="direction:rtl;text-align:right;  padding-top:100px;font-size: 22px;font-weight:normal;color:#000">
               إلى من يهمه الأمر،
تحية طيبة وبعد..
            </td>
        </tr>
		<tr>
            <td colspan="3"  style="direction:rtl;text-align:center;  padding-top:100px;font-size: 22px;font-weight:normal;color:#000">
               تشهد شركة الرجاء الدولية للتجاره والاستثمار بأن المتدرب '.$studentName.' قد تدرب لدينا في '.$courseNamear.' من تاريخ '.$startDate.' ولغاية تاريخ '.$endDate.'.

            </td>
        </tr>
		<tr>
            <td colspan="3"  style="direction:rtl;text-align:right;  padding-top:100px;font-size: 22px;font-weight:normal;color:#000">
              وقد استخرجت هذه الشهادة بناء على طلبه ودون أدنى مسؤولية على الشركة. 
			  <br/>
وتفضلوا بقبول فائق الاحترام والتقدير، 
            </td>
        </tr>
		
        <tr>
            <td colspan="3" class="student-name" style=" padding-top:205px; ">
               <img src="' . $blue . '"/>
            </td>
        </tr>
         <tr>
            <td colspan="3" class="student-name" style=" padding-top:250px; ">
               <img src="' . $footer . '"/>
            </td>
        </tr>
    </table>
</div>
';
 
// Write HTML to PDF
$mpdf->WriteHTML($html);
$savePath = public_path('certificates/'.$fileToSave.'/'); // Adjust this path as needed

// Ensure the folder exists, create it if it doesn't
if (!file_exists($savePath)) {
    mkdir($savePath, 0777, true); // Create the folder with proper permissions
}
// Define the PDF file name
$fileName = 'experience_' .$studentName.'_'.uniqid(). '.pdf'; // Dynamic file name to avoid overwriting
// Full path for the PDF file
$filePath = $savePath . $fileName;
// Save the PDF to the folder \Mpdf\Output\Destination::FILE
$mpdf->Output($filePath,\Mpdf\Output\Destination::FILE);

 
 }

}
