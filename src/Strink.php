<?php

namespace Zazalt\Strink;

class Strink extends Extension\Geek
{
    protected $string;

    public function __construct(string $string = null)
    {
        $this->string = $string;
    }

    /**
     * @return Strink
     */
    public static function turn(string $string = null): Strink
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
     * @return Strink
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
     * @return  Strink
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
     * @return Strink
     */
    public function compressSlashes()
    {
        return new static(preg_replace('~(^|[^:])//+~', '\\1/', $this->string));
    }

    /**
     * Compress multiple double quotes to a single one
     *
     * @in  ""Pleașe țest thîs string""
     * @out "Pleașe țest thîs string"
     *
     * @return Strink
     */
    public function compressDoubleQuotes(): Strink
    {
        return new static(preg_replace('/"+/', '"', $this->string));
    }

    /**
     * Compress multiple simple quotes to a single one
     *
     * @in  '''Please 'test\" this string''
     * @out 'Please 'test\" this string'
     *
     * @return Strink
     */
    public function compressSimpleQuotes(): Strink
    {
        return new static(preg_replace("/'+/", "'", $this->string));
    }

    /**
     * Compress multiple simple and double quotes
     *
     * @return Strink
     */
    public function compressQuotes(): Strink
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
     * @return Strink
     */
    public function randomString(int $length = 12, array $keysToUse = [])
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
     * @return Strink
     */
    public function limitedString(int $limit = 10, string $postText = '...', string $cut = 'right')
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
     * @return Strink
     */
    public function snakeCaseToCamelCase(bool $upperCaseFirsLetter = false)
    {
        $this->string = strtolower($this->string);

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
     * @return Strink
     */
    public function camelCaseToSnakeCase()
    {
        $this->string[0] = strtolower($this->string[0]);
        $function = create_function('$c', 'return "_" . strtolower($c[1]);');
        return new static(preg_replace_callback('/([A-Z])/', $function, $this->string));
    }

    /**
     * Remove a list of words from sentence
     *
     * @param   array   $wordsList
     * @return  string
     */
    public function removeWords(array $wordsList)
    {
        foreach ($wordsList as $word) {
            $this->string = preg_replace("/\b{$word}\b/i", '', $this->string);
        }

        return trim($this->compressSpaces($this->string));
    }

    /**
     * Replace/transliterate accented characters with non accented
     * Remove diacritics from a string
     *
     * @return Strink
     */
    public function transliterateUtf8String()
    {
        $sets = [
            'a'     => ['á', 'à', 'â', 'ä', 'ã', 'å', 'ā', 'ă', 'ą', 'ǻ', 'ǎ'],
            'A'     => ['Á', 'À', 'Â', 'Ä', 'Ã', 'Å', 'Ā', 'Ă', 'Ą', 'Ǻ', 'Ǎ'],

            'ae'    => ['æ', 'ǽ'],
            'AE'    => ['Æ', 'Ǽ'],

            'c'     => ['ç', 'ć', 'ĉ', 'ċ', 'č'],
            'C'     => ['Ç', 'Ć', 'Ĉ', 'Ċ', 'Č'],

            'd'     => ['đ', 'ď'],
            'D'     => ['Ð', 'Đ', 'Ď'],

            'e'     => ['é', 'è', 'ê', 'ë', 'ē', 'ĕ', 'ė', 'ę', 'ě'],
            'E'     => ['É', 'È', 'Ê', 'Ë', 'Ē', 'Ĕ', 'Ė', 'Ę', 'Ě'],

            'f'     => ['ƒ'],
            'F'     => ['ſ'],

            'g'     => ['ĝ', 'ğ', 'ġ', 'ģ'],
            'G'     => ['Ĝ', 'Ğ', 'Ġ', 'Ģ'],

            'h'     => ['ĥ', 'ȟ', 'ḧ', 'ḣ', 'ḩ', 'ḥ', 'ḫ', 'ẖ', 'ħ', 'ⱨ'],
            'H'     => ['Ĥ', 'Ȟ', 'Ḧ', 'Ḣ', 'Ḩ', 'Ḥ', 'Ḫ', 'H̱', 'Ħ', 'Ⱨ'],

            'i'     => ['í', 'ì', 'ĭ', 'î', 'ǐ', 'ï', 'ḯ', 'ĩ', 'į', 'ī', 'ỉ', 'ȉ', 'ȋ', 'ị', 'ḭ', 'ɨ', 'ᵻ', 'ᶖ', 'ı'],
            'I'     => ['Í', 'Ì', 'Ĭ', 'Î', 'Ǐ', 'Ï', 'Ḯ', 'Ĩ', 'Į', 'Ī', 'Ỉ', 'Ȉ', 'Ȋ', 'Ị', 'Ḭ', 'Ɨ', 'ꟾ', 'İ'],

            'j'     => ['ĵ'],
            'J'     => ['Ĵ'],

            'k'     => ['ķ'],
            'K'     => ['Ķ'],

            'l'     => ['ĺ', 'ļ', 'ľ', 'ŀ', 'ł'],
            'L'     => ['Ĺ', 'Ļ', 'Ľ', 'Ŀ', 'Ł'],

            'n'     => ['ń', 'ǹ', 'ň', 'ñ', 'ṅ', 'ņ', 'ṇ', 'ṋ', 'ṉ', 'n̈', 'ɲ', 'ƞ', 'ŋ', 'ꞑ', 'ᵰ', 'ᶇ', 'ɳ', 'ȵ'],
            'N'     => ['Ń', 'Ǹ', 'Ň', 'Ñ', 'Ṅ', 'Ņ', 'Ṇ', 'Ṋ', 'Ṉ', 'N̈', 'Ɲ', 'Ƞ', 'Ŋ', 'Ꞑ', '₦'],

            'o'     => ['ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ō', 'ŏ', 'ő', 'ǒ', 'ǿ', 'ơ'],
            'O'     => ['Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ō', 'Ŏ', 'Ő', 'Ǒ', 'Ǿ', 'Ơ'],

            's'     => ['ś', 'ŝ', 'ş', 'š', 'ș'],
            'S'     => ['Ś','Ŝ', 'Ş', 'Š', 'Ș'],

            't'     => ['ţ', 'ť', 'ŧ', 'ț'],
            'T'     => ['Ţ', 'Ť', 'Ŧ', 'Ț'],

            'u'     => ['ù', 'ú', 'û', 'ü', 'ũ', 'ū', 'ŭ', 'ů', 'ű', 'ų', 'ǔ', 'ǖ', 'ǘ', 'ǚ', 'ǜ'],
            'U'     => ['Ù', 'Ú', 'Û', 'Ü', 'Ũ', 'Ū', 'Ŭ', 'Ů', 'Ű', 'Ų', 'Ǔ', 'Ǖ', 'Ǘ', 'Ǚ', 'Ǜ'],

            'y'     => ['ŷ', 'ý', 'ÿ'],
            'Y'     => ['Ŷ', 'Ý', 'Ÿ'],

            'z'     => ['ź', 'ż', 'ž',],
            'Z'     => ['Ź', 'Ż', 'Ž'],

            '¿'     => ['?'],

            "'"     => ['´']
        ];

        //  'ß',      , '', , ' 'Ĳ',  'ĳ',  'ŉ',  'Œ',  'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř',  'ų', 'Ŵ', 'ŵ', , 'Ư', 'ư'   ],

        foreach ($sets as $replacer => $accents) {
            foreach ($accents as $accent) {
                $this->string = str_replace($accent, $replacer, $this->string);
            }
        }

        return new static($this->string);
    }

    /**
     * Convert a string to-a-slug-one
     *
     * @return Strink
     * @docs    http://stackoverflow.com/questions/2955251/php-function-to-make-slug-url-string
     */
    public function slugify(bool $keepUTF8Chars = false)
    {
        /* Not used yet:
        preg_match_all('/[A-Z]/', $this->string, $match);
        $caseUpper = count($match[0]);
        */

        preg_match_all('/[a-z]/', $this->string, $match);
        $caseLower = count($match[0]);

        if($caseLower > 0) {
            $this->string = $this->camelCaseToSnakeCase($this->string);
        }

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