<html>
<head>
<title></title>
</head>
<body>
<form action="" method="POST">
<input type="text" name="url" placeholder="Full URL of GitHub repository" size="60">
<input type="submit" name="submitButton">
</form>
</body>
</html>

<?php

if(isset($_POST['submitButton']))
{
    //Example-> https://github.com/Shippable/support/issues
    $input_url = $_POST['url'];
    //Break the input url in array format
    $input_url_array =  explode('/',$input_url);

    //Validate the input url
    if(strcmp($input_url_array[0],"https:")||strcmp($input_url_array[1],"")||strcmp($input_url_array[2],"github.com")||empty($input_url_array[3])||empty($input_url_array[4]))
    {
        die("</br>Invalid Url !!! Url should be in format <b>https://github.com/{org_name or username}/{repo_name}/</b><br>");
    }

    //url for the github Api, $input_url_array[3] contain organisation or username, put_url_array[3] contain repository name
    $url = "https://api.github.com/repos/".$input_url_array[3]."/".$input_url_array[4];
    //call the function and receive the result in associative array format
    $result = curlRequestOnGitApi($url);
    //Get total no of open issues using the $result array
    $total_open_issues = $result["open_issues_count"];
    echo "<br>Total Open Issues:<b>".$total_open_issues."</b><br>";


    //Date and Time 1 day or 24 hours ago in ISO 8601 Format
    $time_last24hr = date('Y-m-d\TH:i:s.Z\Z', strtotime('-1 day', time()));
    //url for the github Api with since parameter equal to time of last 24 hrs that return only issues updated at or after this time 
    $url = "https://api.github.com/repos/".$input_url_array[3]."/".$input_url_array[4]."/issues?since=".$time_last24hr;     
    //call the function and receive the result in associative array format
    $result = curlRequestOnGitApi($url);
    //Get no of open issues that were opened in last 24 hours
    $issues_last24hr = count($result);
    echo "Number of open issues that were opened in the last 24 hours:<b>".$issues_last24hr."</b><br>";


    //Date and Time 1 day or 24 hours ago in ISO 8601 Format
    $time_7daysago = date('Y-m-d\TH:i:s.Z\Z', strtotime('-7 day', time()));
    //url for the github Api with since parameter equal to time of 7 days ago that return only issues updated at or after this time 
    $url = "https://api.github.com/repos/".$input_url_array[3]."/".$input_url_array[4]."/issues?since=".$time_7daysago;
    //call the function and receive the result in associative array format
    $result = curlRequestOnGitApi($url);
    //Get no of open issues that were opened in 7 days ago
    $issues_last7days = count($result);
    echo "Number of open issues that were opened more than 24 hours ago but less than 7 days ago:<b>".($issues_last7days-$issues_last24hr)."</b><br>";


    echo "Number of open issues that were opened more than 7 days ago:<b>".($total_open_issues-$issues_last7days)."</b><br>";
}       

function curlRequestOnGitApi($url)
{
    $ch = curl_init();

    //Set the url
    curl_setopt($ch, CURLOPT_URL,$url);

    //Set the User Agent as username
    curl_setopt($ch, CURLOPT_USERAGENT, "anyusername");

    //Accept the response as json
    curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Accept: application/json'));

    //Will return the response, if false it print the response
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute
    $result=curl_exec($ch);

    // Closing
    curl_close($ch);

    //Decode the json in array
    $new_result=json_decode($result,true);

    //Return array
    return $new_result;
}

?>
