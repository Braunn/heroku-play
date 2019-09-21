<?php
    ini_set('display_errors',1);
    ini_set('display_startup_errors',1);
    error_reporting(E_ALL);

    //this is the basic way of getting a database handler from PDO, PHP's built in quasi-ORM
    $dbhandle = new PDO("sqlite:scrabble.sqlite") or die("Failed to open DB");
    if (!$dbhandle) die ($error);

    //this is a sample query which gets some data, the order by part shuffles the results
    //the limit 0, 10 takes the first 10 results.
    // you might want to consider taking more results, implementing "pagination",
    // ordering by rank, etc.
    $query = "SELECT rack, words FROM racks WHERE length=7 order by random() limit 1";

    //this next line could actually be used to provide user_given input to the query to
    //avoid SQL injection attacks
    $statement = $dbhandle->prepare($query);
    $statement->execute();

    //The results of the query are typically many rows of data
    //there are several ways of getting the data out, iterating row by row,
    //I chose to get associative arrays inside of a big array
    //this will naturally create a pleasant array of JSON data when I echo in a couple lines
    $results = $statement->fetchAll(PDO::FETCH_ASSOC);
    $results = json_encode($results);
    echo "<h1>$results<h2>";
    $results = $results.implode(",",makeCombos($results->rack));

    //this part is perhaps overkill but I wanted to set the HTTP headers and status code
    //making to this line means everything was great with this request
    header('HTTP/1.1 200 OK');
    //this lets the browser know to expect json
    header('Content-Type: application/json');
    //this creates json and gives it back to the browser
    echo $results;

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
