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
    public function __construct($pincode = NULL)
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
        $req = new HTTP_Request($link,
                                array('timeout', 3));

        if (PEAR::isError($req->sendRequest())) {
            throw new Exception('Could not send the request: ' . $req->getMessage());
        }

        $xml = $req->getResponseBody();

        preg_match("/<a class=\"fn expand\" href=\"#\"><span>.*<\/span><\/a><\/h3>/", $xml, $name);
        $name = array_map('strip_tags', $name);

        if (strpos($xml, '<span class="place-name">')) {
            preg_match("/<p class=\"adr\">  <span class=\"street-address\">.* <\/span>  <span class=\"place-name\">/", $xml, $address);
            $address = array_map('strip_tags', $address);
        } else {
            preg_match("/<p class=\"adr\">  <span class=\"street-address\">.* <\/span>  <span class=\"postal-code\">/", $xml, $address);
            $address = array_map('strip_tags', $address);
        }

        preg_match("/<span class=\"place-name\">.*<\/span>/", $xml, $place);
        $place = array_map('strip_tags', $place);
        
        preg_match("/<span class=\"postal-code\">.*<\/span>&nbsp;<span class=\"locality\">/", $xml, $postalcode);
        $postalcode = array_map('strip_tags', $postalcode);
        $postalcode = array_map(create_function('$pc', 'return substr($pc, 0, 4);'), $postalcode); 

        preg_match("/<span class=\"locality\">.*<\/span><br\/> <\/p>/", $xml, $locality);
        $locality = array_map('strip_tags', $locality);


        $data = array('navn'    => $this->replaceCharacters($this->getValue($name)),
                      'adresse' => $this->replaceCharacters($this->getValue($address)),
                      'postnr'  => $this->replaceCharacters($this->getValue($postalcode)),
                      'postby'  => $this->replaceCharacters($this->getValue($locality)));

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
        if (!empty($value[0])) {
            return $value[0];
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
?>