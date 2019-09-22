<?php
    // show errors
    ini_set('display_errors',1);
    ini_set('display_startup_errors',1);
    error_reporting(E_ALL);

    //this is the basic way of getting a database handler from PDO, PHP's built in quasi-ORM
    $dbhandle = new PDO("sqlite:scrabble.sqlite") or die("Failed to open DB");
    if (!$dbhandle) die ($error);

    //get query response
    //gets random rack length of 7
    $maxRackLength = "7";
    $query = "SELECT rack, words FROM racks WHERE length=".$maxRackLength." order by random() limit 1";
    $statement = $dbhandle->prepare($query);
    $statement->execute();

    //this will naturally create a pleasant array of JSON data when I echo in a couple lines
    // $results is an array
    $results = $statement->fetchAll(PDO::FETCH_ASSOC);

    /*
    // GETS WORDS FROM SUBRACKS
    $subracks = makeCombos($results[0]["rack"]);
    $numberOfSubracks = count($subracks);
    $allWords = Array("words".$maxRackLength=>explode("@@",$results[0]["words"]));

    for($i = 0; $i < $numberOfSubracks; $i++){
        $subrack = $subracks[$i];
        $subrackLen = strval(strlen($subrack)); // gets length of words that you could make with this rack

        if($subrackLen == 1){
            // check for a and I
            if($subrack == "a" or $subrack == "i"){
                insertIntoAllWords($subrack, $subrackLen, $allWords);
            }
        }
        elseif($subrackLen == 0){
            // do nothing!
        }
        else {
            // prepare and get query
            echo "<h1>HEY subrack:".$subrack."<h1>";
            $getWordsQuery = "SELECT words FROM racks WHERE rack='".$subrack."'";
            $statement0 = $dbhandle->prepare($getWordsQuery);
            $statement0->execute();
            $response = $statement0->fetchAll(PDO::FETCH_ASSOC); // gets key value array rack,words index 2

            echo "<h1>HEY response:".gettype($response)."<h1>";

            // ensures the subrack has some words
            if($response[0] == null){
                // do nothing!
            }
            elseif(array_key_exists("words",$response[0])){
                insertIntoAllWords($response[0]["words"], $subrackLen, $allWords);
            }
        }

    }
    */
    //this part is perhaps overkill but I wanted to set the HTTP headers and status code
    //making to this line means everything was great with this request
    header('HTTP/1.1 200 OK');
    //this lets the browser know to expect json
    header('Content-Type: application/json');
    //this creates json and gives it back to the browser
    echo json_encode($results[0]);

    //
    // HELPER FUNCTIONS
    //

    function insertIntoAllWords(String $istr, String $length, Array $allWords){
        // checks if array key exists and handles insertion accordingly
        $key = "words".$length;
        if(array_key_exists($key,$allWords)){
            $allWords[$key] = array_merge( $allWords[$key], explode("@@", $istr));
        }
        else{
            $allWords[$key] = explode("@@", $istr);
        }
    }

    // creates an array with all the combos of chars in aString
    // combo strings will be in alphabetical order as long as input string is
    // POTENTIAL PROBLEM: second element of array will be empty string
    function makeCombos(String $aString){
          $comboList = Array($aString[0],"");
          $aLen = strlen($aString);
          $index = 1;
          while($index < $aLen){
              //echo implode(" ",$comboList)."<br>";
              $comboList = array_merge($comboList, distChar($comboList, $aString[$index]));
              $index ++;
          }

          return $comboList;
      };

      // distributes a string called character amongst all the strings in the $strArray
      // returns an array with all the combos (use implode to cast to string)
      function distChar(Array $strArray, String $character){
        $alen = count($strArray);

        for($i = 0; $i < $alen; $i++){
          $strArray[$i] = $strArray[$i].$character;
        }

        return $strArray;
      };

?>
