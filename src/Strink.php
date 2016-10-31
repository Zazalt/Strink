<?php

namespace Zazalt\Strink;

class Strink extends \Zazalt\Strink\Extension\Geek
{
    protected $string;

    public function __construct($string)
    {
        $this->string = $string;
    }

    /**
     * @return object
     */
    public static function turn($string = null)
    {
        return new static($string);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->string;
    }

    /**
     * Return the string
     *
     * @return string
     */
    public function toString()
    {
        return $this->string;
    }

    /**
     * Compress multiple spaces to a single one
     *
     * @in  The string  you want to   fix
     * @out The string you want to fix
     *
     * @return  object
     */
    public function compressSpaces()
    {
        return new static(preg_replace('/\s\s+/', ' ', $this->string));
    }

    /**
     * Compress multiple slashes to a single one (from many ///// to one /)
     *
     * @in  this/is/a//very/bad/uri//
     * @out this/is/a/very/bad/uri/
     *
     * @return  object
     */
    public function compressSlashes()
    {
        return new static(preg_replace('/\\/+/', '/', $this->string));
    }

    /**
     * Compress multiple double quotes to a single one
     *
     * @in  ""Pleașe țest thîs string""
     * @out "Pleașe țest thîs string"
     *
     * @return  object
     */
	public function compressDoubleQuotes() {
		return new static(preg_replace('/"+/', '"', $this->string));
	}

    /**
     * Compress multiple simple quotes to a single one
     *
     * @in  '''Please 'test\" this string''
     * @out 'Please 'test\" this string'
     *
     * @return  object
     */
	public function compressSimpleQuotes() {
		return new static(preg_replace("/'+/", "'", $this->string));
	}

    /**
     * Compress multiple simple and double quotes
     *
     * @return  object
     */
    public function compressQuotes()
    {
        $modifiedString = $this
            ->compressSimpleQuotes()
            ->compressDoubleQuotes()
            ->compressSimpleQuotes()
            ->compressDoubleQuotes()
            ->toString();

        return new static($modifiedString);
    }

    /**
     * Generate a random string
     *
     * @param   integer      $length
     * @param   multi-array  $keysToUse
     * @return  object
     */
    public function randomString($length = 12, array $keysToUse = [])
    {
        if(is_array($keysToUse) AND count($keysToUse) == 0) {
            $keysToUse = [
                'abcdefghijklmnopqrstuwxyz',
                'ABCDEFGHIJKLMNOPQRSTUWXYZ',
                '0123456789',
                '!@#$%^&*+='
            ];
        }

        $password = '';
        $index = 0;
        while(true) {
            if($index > (count($keysToUse) - 1)) {
                $index = 0;
            }

            $password .= $keysToUse[$index][mt_rand(0, (strlen($keysToUse[$index]) - 1))];

            if(strlen($password) >= $length) {
                break;
            }

            ++$index;
        }

        return new static($password);
    }

    /**
     * Make a string shorter
     *
     * @param	integer	$limit
     * @param	string	$postText
     * @return  object
     */
    public function limitedString($limit = 10, $postText = '...', $cut = 'right')
    {
        if(strlen($this->string) > $limit) {

            $limit = $limit+1;

            if($cut == 'right') {
                return mb_substr($this->string, 0, ($limit - count($postText)), 'utf-8') . $postText;
            } elseif($cut == 'middle' OR $cut == 'center') {
                $return = mb_substr($this->string, 0, (round($limit / 2) - count($postText)), 'utf-8');
                $return .= $postText;
                $return .= mb_substr($this->string, count($this->string) - round($limit / 2), strlen($this->string), 'utf-8');
            }
        }

        return new static(isset($return) ? $return : $this->string);
    }

    /**
     * Transform a snake_case string to camelCase or CamelCase
     * Translates a string with underscores into camel case (e.g. first_name -> firstName)
     *
     * @param   boolean $upperCaseFirsLetter
     * @return  object
     */
    public function snakeCaseToCamelCase($upperCaseFirsLetter = false)
    {
        if(strpos($this->string, '_')) {
            $function = create_function('$c', 'return strtoupper($c[1]);');
            $this->string = preg_replace_callback('/_([a-z])/', $function, $this->string);
        }

        return new static($upperCaseFirsLetter ? ucfirst($this->string) : $this->string);
    }

    /**
     * Transform a camelCase string to snake_case
     * Translates a camel case string into a string with underscores (e.g. firstName -> first_name)
     *
     * @return  object
     */
    public function camelCaseToSnakeCase()
    {
        $this->string[0] = strtolower($this->string[0]);
        $function = create_function('$c', 'return "_" . strtolower($c[1]);');
        return new static(preg_replace_callback('/([A-Z])/', $function, $this->string));
    }

    /**
     * Replace/transliterate accented characters with non accented
     * Remove diacritics from a string
     *
     * @return  object
     */
    public function transliterateUtf8String()
    {
        return new static(str_replace(
            ['À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ',  'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ',  'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı',  'Ĳ',  'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő',  'Œ',  'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ',  'Ǽ',  'ǽ', 'Ǿ', 'ǿ', 'ș', 'Ș', 'ț', 'Ț'],
            ['A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o', 's', 'S', 't', 'T'],
            $this->string
        ));
    }

    /**
     * Convert a string to-a-slug-one
     *
     * @return  object
     * @docs    http://stackoverflow.com/questions/2955251/php-function-to-make-slug-url-string
     */
    public function slugify($keepUTF8Chars = false)
    {
        $this->string = $this->camelCaseToSnakeCase($this->string);

        // replace non letter or digits by -
        $this->string = preg_replace('/[^\pL\d]+/u', '-', $this->string);

        // transliterate
        if(!$keepUTF8Chars) {
            $this->string = $this->transliterateUtf8String($this->string);

            $this->string = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->string);

            // remove unwanted characters
            $this->string = preg_replace('/[^-\w]+/', '', $this->string);
        }

        // trim
        $this->string = trim($this->string, '-');

        // remove duplicate -
        $this->string = preg_replace('/-+/', '-', $this->string);

        // lowercase
        $this->string = mb_strtolower($this->string, mb_detect_encoding($this->string));

        return new static(!empty($this->string) ? $this->string : null);
    }
}