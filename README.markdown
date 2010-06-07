PHP class to talk to Eniro.dk
==

Usage
--

    $eniro = new Services_Eniro();
    $result = $eniro->query('telefon', $phonenumber);
    var_dump($result);