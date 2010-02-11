<?php
/**
 * Communicates with Eniro's REST service
 *
 * PHP version 5
 *
 * TODO get the encoding correct
 *
 * @category  Services
 * @package   Services_Eniro
 * @author    Lars Olesen <lars@legestue.net>
 * @license   LGPL
 * @copyright 2007 Lars Olesen
 * @version   @package-version@
 */
require_once 'HTTP/Request.php';

/**
 * Communicates with Eniro's REST service
 *
 * <code>
 * $eniro = new Services_Eniro(PINCODE);
 * $result = $eniro->query('telefon', $phonenumber);
 * var_dump($result);
 * </code>
 *
 * @category  Services
 * @package   Services_Eniro
 * @author    Lars Olesen <lars@legestue.net>
 * @license   LGPL
 * @copyright 2007 Lars Olesen
 * @version   @package-version@
 */
class Services_Eniro {

    /**
     * @var string
     */
    protected $pincode;

    /**
     * Constructor
     *
     * @param int $pincode Not necessary but it would be clever to maintain for newer versions
     *
     * @return void
     */
    public function __construct($pincode = null)
    {
        $this->pincode = $pincode;
    }

    /**
     * Queries the Eniro REST service
     *
     * @param string $field Field to search in
     * @param string $query What to search for
     *
     * @return array with the address
     */
    public function query($field, $query)
    {
        $link = 'http://person.eniro.dk/query?what=wp&lang=&search_word=' . $query;
        $req = new HTTP_Request($link, array('timeout', 3));

        if (PEAR::isError($req->sendRequest())) {
            throw new Exception('Could not send the request: ' . $req->getMessage());
        }

        $xml = $req->getResponseBody();
        preg_match("/<span class=\"given-name\"\>(.*)<\/span>/", $xml, $given_name);
        preg_match("/<span class=\"family-name\"\>(.*)<\/span>/", $xml, $family_name);

        $name = $this->getValue($given_name) . ' ' . $this->getValue($family_name);
        $name = $this->replaceCharacters($name);

        preg_match("/<span class=\"street-address\">(.*)<\/span>/", $xml, $address);
        $address = $this->getValue($address);
        $address = $this->replaceCharacters($address);

        preg_match("/<span class=\"place-name\">(.*)<\/span>/", $xml, $place);
        $place = $this->getValue($place);
        $place = $this->replaceCharacters($place);
        if(!empty($place)) {
            $address .= ', '.$place;
        }

        preg_match("/<span class=\"postal-code\">\s*(.*)\s*<\/span>/", $xml, $postalcode);
        $postalcode = $this->getValue($postalcode);
        $postalcode = $this->replaceCharacters($postalcode);

        preg_match("/<span class=\"locality\">\s*(.*)\s*<\/span>/", $xml, $locality);
        $locality = $this->getValue($locality);
        $locality = $this->replaceCharacters($locality);

        $data = array('navn'    => $name,
                      'adresse' => $address,
                      'postnr'  => $postalcode,
                      'postby'  => $locality);

        $data = array_map('trim', $data);

        if (empty($data)) {
            return array('navn'    => 'Ingen data fundet',
                         'adresse' => '',
                         'postnr'  => '',
                         'postby'  => ''
            );
        }

        return $data;
    }

    protected static function getValue($value)
    {
        if (!empty($value[1])) {
            return strip_tags($value[1]);
        } else {
            return '';
        }
    }

    protected static function replaceCharacters($phrase)
    {
        $healthy = array('&oslash;', '&aelig;', '&Aring;', '&aring;', '&Aelig;', '&Oslash;');
        $yummy = array('ø', 'æ', 'Å', 'å', 'Æ', 'Ø');
        return str_replace($healthy, $yummy, $phrase);
    }
}