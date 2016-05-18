<?php

/**
 * Don't give direct access to the template
 */ 
if(!class_exists("RGForms")){
	return;
}

/**
 * Load the form data to pass to our PDF generating function 
 */
$form = RGFormsModel::get_form_meta($form_id);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>	
    <link rel='stylesheet' href='<?php echo get_stylesheet_directory_uri() . '/assets/css/styles.css'; ?>' type='text/css' />
	<link rel='stylesheet' href='<?php echo get_stylesheet_directory_uri() . '/assets/css/bootstrap.min.css'; ?>' type='text/css' />
    <title>RAS-DS Online</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
	<body>
        <?php

        foreach($lead_ids as $lead_id) {

            $lead = RGFormsModel::get_lead($lead_id);
            $form_data = GFPDFEntryDetail::lead_detail_grid_array($form, $lead);

			/*
			 * Add &data=1 when viewing the PDF via the admin area to view the $form_data array
			 */
			PDF_Common::view_data($form_data);				
						
			/*
			 * Store your form fields from the $form_data array into variables here
			 * To see your entire $form_data array, view your PDF via the admin area and add &data=1 to the url
			 * 
			 * For an example of accessing $form_data fields see https://developer.gravitypdf.com/documentation/custom-templates-introduction/
			 *
			 * Alternatively, as of v3.4.0 you can use merge tags (except {allfields}) in your templates. 
			 * Just add merge tags to your HTML and they'll be parsed before generating the PDF.	
			 * 		 
			 */

			?>
        <h2 class="mar-0 mar-b-4 text-9">
	        Results: <span class="text-regular c-gray">{date_dmy}</span>
        </h2>

        <div class="bg-light-gray pad-y-2 pad-x-2 mar-b-4">
	        <b>Key</b>:
	        <span class="display-inline-block pad-r-1 pad-l-1"><b>1</b> = Untrue</span>
	        <span class="display-inline-block pad-r-1 pad-l-1"><b>2</b> = A Bit True</span>
	        <span class="display-inline-block pad-r-1 pad-l-1"><b>3</b> = Mostly True</span>
	        <span class="display-inline-block pad-r-1 pad-l-1"><b>4</b> = Completely True</span>
        </div>

        <h3 class="mar-t-0 mar-b-3">
	        Doing Things I Value
        </h3>
        <table class="table table-border">
	        <thead>
	        <tr>
		        <th class="col-sm-7">Question</th>
		        <th class="col-sm-1">Score</th>
		        <th class="col-sm-4">Comment</th>
	        </tr>
	        </thead>
	        <tbody>
	        <tr>
		        <td><span id="q1-question">1: It is important to have fun</span></td>
		        <td>{1: It is important to have fun:4}</td>
		        <td><em>{Comment:5}</em></td>
	        </tr>
	        <tr>
		        <td><span id="q2-question">2: It is important to have healthy habits</span></td>
		        <td>{2: It is important to have healthy habits:6}</td>
		        <td><em>{Comment:7}</em></td>
	        </tr>
	        <tr>
		        <td><span id="q3-question">3: I do things that are meaningful to me</span></td>
		        <td>{3: I do things that are meaningful to me:8}</td>
		        <td><em>{Comment:9}</em></td>
	        </tr>
	        <tr>
		        <td><span id="q4-question">4: I continue to have new interests</span></td>
		        <td>{4: I continue to have new interests:10}</td>
		        <td><em>{Comment:11}</em></td>
	        </tr>
	        <tr>
		        <td><span id="q5-question">5: I do things that are valuable and helpful to others</span></td>
		        <td>{5: I do things that are valuable and helpful to others:12}</td>
		        <td><em>{Comment:13}</em></td>
	        </tr>
	        <tr>
		        <td><span id="q6-question">6: I do things that give me a feeling of great pleasure</span></td>
		        <td>{6: I do things that give me a feeling of great pleasure:14}</td>
		        <td><em>{Comment:15}</em></td>
	        </tr>
	        <tr>
		        <td><b>Score:</b></td>
		        <td><b>{Total 1:86}</b></td>
		        <td></td>
	        </tr>
	        <tr>
		        <td><b>Perentage:</b></td>
		        <td><b>{Percent 1:90}%</b></td>
		        <td></td>
	        </tr>
	        </tbody>
        </table>

	        <h3 class="mar-t-5 mar-b-3">
		        Looking Forward
	        </h3>
	        <table class="table table-border">
		        <thead>
		        <tr>
			        <th class="col-sm-7">Question</th>
			        <th class="col-sm-1">Score</th>
			        <th class="col-sm-4">Comment</th>
		        </tr>
		        </thead>
		        <tbody>
		        <tr>
			        <td><span id="q7-question">7: I can handle it if I get unwell again</span></td>
			        <td>{7: I can handle it if I get unwell again:18}</td>
			        <td><em>{Comment:19}</em></td>
		        </tr>
		        <tr>
			        <td><span id="q8-question">8: I can help myself become better</span></td>
			        <td>{8: I can help myself become better:20}</td>
			        <td><em>{Comment:21}</em></td>
		        </tr>
		        <tr>
			        <td><span id="q9-question">9: I have the desire to succeed</span></td>
			        <td>{9: I have the desire to succeed:22}</td>
			        <td><em>{Comment:23}</em></td>
		        </tr>
		        <tr>
			        <td><span id="q10-question">10: I have goals in life that I want to reach</span></td>
			        <td>{10: I have goals in life that I want to reach:24}</td>
			        <td><em>{Comment:25}</em></td>
		        </tr>
		        <tr>
			        <td><span id="q11-question">11: I believe that I can reach my current personal goals</span></td>
			        <td>{11: I believe that I can reach my current personal goals:26}</td>
			        <td><em>{Comment:27}</em></td>
		        </tr>
		        <tr>
			        <td><span id="q12-question">12: I can handle what happens in my life</span></td>
			        <td>{12: I can handle what happens in my life:28}</td>
			        <td><em>{Comment:29}</em></td>
		        </tr>
		        <tr>
			        <td><span id="q13-question">13: I like myself</span></td>
			        <td>{13: I like myself:30}</td>
			        <td><em>{Comment:31}</em></td>
		        </tr>
		        <tr>
			        <td><span id="q14-question">14: I have a purpose in life</span></td>
			        <td>{14: I have a purpose in life:32}</td>
			        <td>{Comment:33}</td>
		        </tr>
		        <tr>
			        <td><span id="q15-question">15: If people really knew me they would like me</span></td>
			        <td>{15: If people really knew me they would like me:34}</td>
			        <td><em>{Comment:35}</em></td>
		        </tr>
		        <tr>
			        <td><span id="q16-question">16: If I keep trying, I will continue to get better</span></td>
			        <td>{16: If I keep trying, I will continue to get better:36} </td>
			        <td><em>{Comment:37}</em></td>
		        </tr>
		        <tr>
			        <td><span id="q17-question">17: I have an idea of who I want to become</span></td>
			        <td>{17: I have an idea of who I want to become:38}</td>
			        <td><em>{Comment:39}</em></td>
		        </tr>
		        <tr>
			        <td><span id="q18-question">18: Something good will eventually happen</span></td>
			        <td>{18: Something good will eventually happen:40}</td>
			        <td><em>{Comment:41}</em></td>
		        </tr>
		        <tr>
			        <td><span id="q19-question">19: I am the person most responsible for my own improvement</span></td>
			        <td>{19: I am the person most responsible for my own improvement:42}</td>
			        <td><em>{Comment:43}</em></td>
		        </tr>
		        <tr>
			        <td><span id="q20-question">20: I am hopeful about my own future</span></td>
			        <td>{20: I am hopeful about my own future:44}</td>
			        <td><em>{Comment:45}</em></td>
		        </tr>
		        <tr>
			        <td><span id="q21-question">21: I know when to ask for help</span></td>
			        <td>{21: I know when to ask for help:46}</td>
			        <td><em>{Comment:47}</em></td>
		        </tr>
		        <tr>
			        <td><span id="q22-question">22: I ask for help, when I need it</span></td>
			        <td>{22: I ask for help, when I need it:48}</td>
			        <td><em>{Comment:49}</em></td>
		        </tr>
		        <tr>
			        <td><span id="q23-question">23: I know what helps me get better</span></td>
			        <td>{23: I know what helps me get better:50}</td>
			        <td><em>{Comment:51}</em></td>
		        </tr>
		        <tr>
			        <td><span id="q24-question">24: I can learn from my mistakes</span></td>
			        <td>{24: I can learn from my mistakes:52}</td>
			        <td><em>{Comment:53}</em></td>
		        </tr>
		        <tr>
			        <td><b>Score:</b></td>
			        <td><b>{Total 2:87}</b></td>
			        <td></td>
		        </tr>
		        <tr>
			        <td><b>Perentage:</b></td>
			        <td><b>{Percent 2:91}%</b></td>
			        <td></td>
		        </tr>
		        </tbody>
	        </table>


	        <h3 class="mar-t-5 mar-b-3">
		        Mastering My Illness
	        </h3>
	        <table class="table table-border">
		        <thead>
		        <tr>
			        <th class="col-sm-7">Question</th>
			        <th class="col-sm-1">Score</th>
			        <th class="col-sm-4">Comment</th>
		        </tr>
		        </thead>
		        <tbody>
		        <tr>
			        <td><span id="q25-question">25: I can identify the early warning signs of becoming unwell</span></td>
			        <td>{25: I can identify the early warning signs of becoming unwell:56}</td>
			        <td><em>{Comment:57}</em></td>
		        </tr>
		        <tr>
			        <td><span id="q26-question">26: I have my own plan for how to stay or become well</span></td>
			        <td>{26: I have my own plan for how to stay or become well:58}</td>
			        <td><em>{Comment:59}</em></td>
		        </tr>
		        <tr>
			        <td><span id="q27-question">27: There are things that I can do that help me deal with unwanted symptoms</span></td>
			        <td>{27: There are things that I can do that help me deal with unwanted symptoms:60}</td>
			        <td><em>{Comment:61}</em></td>
		        </tr>
		        <tr>
			        <td><span id="q28-question">28. I know that there are mental health services that help me</span></td>
			        <td>{28: I know that there are mental health services that help me:62} </td>
			        <td><em>{Comment:63}</em></td>
		        </tr>
		        <tr>
			        <td><span id="q29-question">29. Although my symptoms may get worse, I know I can handle it</span></td>
			        <td>{29: Although my symptoms may get worse, I know I can handle it:64} </td>
			        <td><em>{Comment:65}</em></td>
		        </tr>
		        <tr>
			        <td><span id="q30-question">30. My symptoms interfere less and less with my life</span></td>
			        <td>{30: My symptoms interfere less and less with my life:66}</td>
			        <td><em>{Comment:67}</em></td>
		        </tr>
		        <tr>
			        <td><span id="q31-question">31. My symptoms seem to be a problem for shorter periods of</span></td>
			        <td>{31: My symptoms seem to be a problem for shorter periods of time each time they occur:68}</td>
			        <td><em>{Comment:69}</em></td>
		        </tr>
		        <tr>
			        <td><span id="q32-question">32. I have people that I can count on</span></td>
			        <td>{32: I have people that I can count on:72}</td>
			        <td><em>{Comment:73}</em></td>
		        </tr>
		        <tr>
			        <td><b>Score:</b></td>
			        <td><b>{Total 3:88}</b></td>
			        <td></td>
		        </tr>
		        <tr>
			        <td><b>Perentage:</b></td>
			        <td><b>{Percent 3:92}%</b></td>
			        <td></td>
		        </tr>
		        </tbody>
	        </table>


	        <h3 class="mar-t-5 mar-b-3">
		        Connecting And Belonging
	        </h3>
	        <table class="table table-border">
		        <thead>
		        <tr>
			        <th class="col-sm-7">Question</th>
			        <th class="col-sm-1">Score</th>
			        <th class="col-sm-4">Comment</th>
		        </tr>
		        </thead>
		        <tbody>
		        <tr>
			        <td><span id="q33-question">33. Even when I don’t believe in myself, other people do</span></td>
			        <td>{33: Even when I don’t believe in myself, other people do:74}</td>
			        <td><em>{Comment:75}</em></td>
		        </tr>
		        <tr>
			        <td><span id="q34-question">34. It is important to have a variety of friends</span></td>
			        <td>{34: It is important to have a variety of friends:76}</td>
			        <td><em>{Comment:77}</em></td>
		        </tr>
		        <tr>
			        <td><span id="q35-question">35. I have friends who have also experienced mental illness</span></td>
			        <td>{35: I have friends who have also experienced mental illness:78}</td>
			        <td><em>{Comment:79}</em></td>
		        </tr>
		        <tr>
			        <td><span id="q36-question">36. I have friends without mental illness</span></td>
			        <td>{36: I have friends without mental illness:80}</td>
			        <td><em>{Comment:81}</em></td>
		        </tr>
		        <tr>
			        <td><span id="q37-question">37. I have friends that can depend on me</span></td>
			        <td>{37: I have friends that can depend on me:82}</td>
			        <td><em>{Comment:83}</em></td>
		        </tr>
		        <tr>
			        <td><span id="q38-question">38. I feel OK about my family situation</span></td>
			        <td>{38: I feel OK about my family situation:84}</td>
			        <td><em>{Comment:85}</em></td>
		        </tr>
		        <tr>
			        <td><b>Score:</b></td>
			        <td><b>{Total 4:89}</b></td>
			        <td></td>
		        </tr>
		        <tr>
			        <td><b>Perentage:</b></td>
			        <td><b>{Percent 4:93}%</b></td>
			        <td></td>
		        </tr>

		        <tr>
			        <td><b class="text-6">Total Score:</b></td>
			        <td><b class="text-6">{Total Score:94}</b></td>
			        <td></td>
		        </tr>
		        <tr>
			        <td><b class="text-6">Total Percentage:</b></td>
			        <td><b class="text-6">{Total Percentage:95}%</b></td>
			        <td></td>
		        </tr>

		        </tbody>
	        </table>
	        <h3>Understanding my results</h3>
	        <p>At the bottom of each domain or area of recovery, there is a total score and there is a percentage score. There is also an overall score and an overall percentage. The “scores” are a total of all of the scores for that section. The "percentage" is the total score divided by the number of items and then converted to a percentage. We think the percentage scores are most useful because they allow you to compare how well you are doing in different domains. Because each domain has different numbers of statements, the total scores for the domains with more statements are likely to be bigger, so it makes it harder to compare. However, it is important to recognise that the percentage scores are not any indication of "passing" or "failing" in any domain. It just makes comparing the different domains easier. In recovery, there is no passing or failing, just progress.</p>

	        <p>Using the percentage scores allows you to see which domains or areas of recovery you are doing best in as well as those ones you might want to work on to improve. However, the area you want to focus on might not be the statement you marked lowest or the domain with the lowest percentage. It is probably going to be the thing or things that you feel are most important to you. What you wrote in the right-hand column might help you decide what matters most to you. We suggest having a conversation with someone else that you trust about what you scored and what you most want to work on now. Speaking aloud often helps clear our thoughts! The RAS-DS workbook on the website might help you think about how to get started on your chosen area. If you want to check out the information and exercises in the workbook, take a note of the domain / area of recovery you want to focus on and click on the "Go to the workbook" button above and explore the workbook for that domain / area.</p>
        <?php } ?>
	</body>
</html>