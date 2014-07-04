<?php
  session_start();

  $passthruurl = base64_decode($_REQUEST["taskurl"]);

  foreach ($_GET as $key=>$value) {
  	if($key != "taskurl") {
      $passthruurl .= $key . "=" . $value;
    }
  }

  $passthru = FALSE;

  // PASSTHRU CONDITIONS
  // 1.  ASSIGNMENT_ID_NOT_AVAILABLE
  if($_REQUEST["assignmentId"] == "ASSIGNMENT_ID_NOT_AVAILABLE") {
  	//header("location: " . $passthruurl);
  	$r = "no assignment";
  	$passthru = TRUE;
  } else if(!isset($_REQUEST["workerId"])) {
	// 2.  no workerId
  	$r = "no workerid";

  	$passthru = TRUE;
  } else {
  	// Here begins database accesses.
	$filename = dirname(__FILE__) . '/turkers.db';
	//unlink($filename);
	$db = new PDO('sqlite:' . $filename);
	$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

	// 1. create a sqlite database table with the following columns if it does not already exist: id, timestamp, workerid
	$stmt = $db->prepare('CREATE TABLE IF NOT EXISTS consent (id INTEGER PRIMARY KEY AUTOINCREMENT, timestamp TIMESTAMP, workerid varchar(32))');
	$stmt->execute();

	// workerId
	$wid = $_REQUEST["workerId"];

	// 2. check for workerId
	$stmt = $db->prepare('select id from consent where workerid = :wid');
    $stmt->execute(array(':wid' => $wid));
	$rows = $stmt->fetch(PDO::FETCH_NUM);

	// 3. if no workerId found, insert it,
	//    but only if the user agreed to everything.
	$now = date('Y-m-d H:i:s');
	if($rows === FALSE) {
		if(isset($_REQUEST["age18"]) && isset($_REQUEST["understood"]) && isset($_REQUEST["wantto"]) &&
  			$_REQUEST["age18"]=="yes" && $_REQUEST["understood"]=="yes" && $_REQUEST["wantto"]=="yes") {
   			$stmt = $db->prepare('INSERT INTO consent (timestamp, workerid) VALUES(:timestamp, :wid)');
    		$ret = $stmt->execute(array(':wid' => $wid, ':timestamp' => $now));
    		$passthru = TRUE;
		  	$r = "workerid doesn't exist, agree";

    	} else {
    		$r = "workerid doesn't exist, no agree";
    	}
	}
	else {
		$r = "exists";
		$passthru = TRUE;
	}
  }
if($passthru == TRUE) {
	//header("location: " . )
}
?>
<!doctype html>
<html>
<head>
<title>Augmenting Computation with Input From People</title>
<style>
html,body {
	font-family: verdana, sans serif;
	font-size: 0.9em;
	height: 100%;
}
.container {
	height: 70%;
	overflow: scroll;	
	border: 3px solid #aaa;
}
.container,.consent {
	width: 60%;
	margin: auto;
	padding: 5px;
}
input[type=submit],input[type=checkbox] {
	line-height: 2.5em;
	font-size: 3.5em;
	font-weight: bold;
}
</style>
</head>
<body>
<?php
//echo "PASS" . $passthru . " -- " . $r . "\n";
?>
<p class="consent" style="padding: 2em 0">
Please read the information below, complete the three questions below it, and click the button to continue to the task. You will only need to do this step once for all of our tasks.
</p>
<div class="container">
<h1>Augmenting Computation with Input From People</h1>

<p>
This task is part of a research study conducted by Jeffrey Bigham at Carnegie Mellon University and is funded by Carnegie Mellon University.
</p>

<p>
The purpose of the research is to understand how small bits of human work may be able to allow computers to perform tasks that they cannot do currently, e.g. reliably convert speech to text, understand the visual information in images, or respond naturally to natural language questions.
</p>


<h2>Procedures</h2>
<p>
If you participate, you will be asked to complete or simply view one or more interaction tasks, which may involve entering a text description (e.g., describing an image or sound), controlling an interface (e.g., clicking buttons, navigating an avatar to a specified target), or typing responses to provided questions (e.g., answering, "what is a good restaurant in Pittsburgh?"). The duration of these tasks will be at least one minute and no more than one hour. You may quit after any task and be compensated proportional to the time spent.
</p>


<h2>Participant Requirements</h2>
<p>
Participation in this study is limited to individuals aged 18 and older.
</p>

<h2>Risks</h2>
<p>
The risks and discomfort associated with participation in this study are no greater than those ordinarily encountered in daily life or during other online activities. As with other tasks in your daily life, you may tire or become bored completing our tasks. There is also a risk of confidentiality being breached.
</p>

<h2>Benefits</h2>
<p>
There may be no personal benefit from your participation in the study but the knowledge received may be of value to humanity.
</p>

<h2>Compensation &amp; Costs</h2>
<p>
You will be compensated for your participation in this study at the rate of $10 per hour. There will be no cost to you if you participate in this study. If you decide to end the Study early, you will be compensated for the portion that you completed.
</p>

<h2>Confidentiality</h2>
<p>
The data captured for the research does not include any personally identifiable information about you.  Your IP address will not be captured.
</p>

<h2>Right to Ask Questions &amp; Contact Information</h2>
<p>
If you have any questions about this study, you should feel free to ask them by contacting the Principal Investigator now at Jeffrey P. Bigham, Human-Computer Interaction Institute, 5000 Forbes Ave, Pittsburgh, PA 15213. (412) 945-0708, jbigham@cs.cmu.edu. If you have questions later, desire additional information, or wish to withdraw your participation please contact the Principal Investigator by mail, phone or e-mail in accordance with the contact information listed above.  
</p>

<p>
If you have questions pertaining to your rights as a research participant; or to report objections to this study, you should contact the Office of Research integrity and Compliance at Carnegie Mellon University.  Email: irb-review@andrew.cmu.edu . Phone: 412-268-1901 or 412-268-5460.
</p>

<h3>Voluntary Participation</h3>
<p>
Your participation in this research is voluntary.  You may discontinue participation at any time during the research activity.  
</p>
</div>
<div class="consent" style="background-color: #EEE">
<form method="post">
<?php
foreach ($_GET as $key=>$value) {
  echo "<input type='hidden' name='" . $key . "' value='" . $value . "'>\n";
}
?>
<input type="hidden" name="taskurl" value="<?= $_REQUEST["taskurl"] ?>">
<input type="hidden" name="submitting" value="true">
<p>
<label><input type="radio" name="age18" value="yes">Yes</label>&nbsp;<label><input type="radio" name="age18" value="no">No</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
I am age 18 or older.
<br>
<label><input type="radio" name="understood" value="yes">Yes</label>&nbsp;<label><input type="radio" name="understood" value="no">No</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
I have read and understand the information above.
<br>
<label><input type="radio" name="wantto" value="yes">Yes</label>&nbsp;<label><input type="radio" name="wantto" value="no">No</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
I want to participate in this research and continue with the task.
</p>
<p>
<input type="submit" value="Continue to the Task">
</p>
</form>
</div>
</body>
</html>

