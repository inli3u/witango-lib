<?php

namespace Witangone;


class WitangoLib
{
    /**
     * %a abbreviated weekday name 
     * %A full weekday name
     * %b abbreviated month name
     * %B full month name 
     * %c local date and time representation
     * %d day of month (01–31) 
     * %H hour (24 hour clock) 
     * %I hour (12 hour clock)
     * %j day of the year (001–366) 
     * %m month (01–12) 
     * %M minute (00–59) 
     * %p local equivalent of AM or PM 
     * %S second (00–59) 
     * %U week number of the year (Sunday= first day of week) (00–53) 
     * %w weekday (0–6, Sunday is zero) 
     * %W week number of the year (Monday = first day of week) (00–53) 
     * %x local date representation 
     * %X local time representation
     * %y year without century (00–99)
     * %Y year with century
     * %% % sign 
     */
    private static function date_part($format, $time)
    {
        switch ($format) {
            case 'a': return date('D', $time); //  abbreviated weekday name 
            case 'A': return date('l', $time); //  full weekday name
            case 'b': return date('M', $time); //  abbreviated month name
            case 'B': return date('F', $time); //  full month name 
            case 'c': throw new Exception('Date format not supported: ' . $format); //  local date and time representation
            case 'd': return date('d', $time); //  day of month (01–31) 
            case 'H': return date('G', $time); //  hour (24 hour clock) 
            case 'I': return date('g', $time); //  hour (12 hour clock)
            case 'j': return date('z', $time) + 1; //  day of the year (001–366) 
            case 'm': return date('m', $time); //  month (01–12) 
            case 'M': return date('i', $time); //  minute (00–59) 
            case 'p': return date('A', $time); //  local equivalent of AM or PM 
            case 'S': return date('s', $time); //  second (00–59) 
            case 'U': throw new Exception('Date format not supported: ' . $format); //  week number of the year (Sunday= first day of week) (00–53) 
            case 'w': return date('w', $time); //  weekday (0–6, Sunday is zero) 
            case 'W': return date('W', $time) - 1; //  week number of the year (Monday = first day of week) (00–53) 
            case 'x': throw new Exception('Date format not supported: ' . $format); //  local date representation 
            case 'X': throw new Exception('Date format not supported: ' . $format); //  local time representation
            case 'y': return date('y', $time); //  year without century (00–99)
            case 'Y': return date('Y', $time); //  year with century
            case '%': return '%'; //  % sign 
        }
    }

    private static function date($format, $time = null) {
        if ($time === null) {
            $time = time();
        }
        
        $i = 0;
        $formatted = '';
        while ($i < strlen($format)) {
            $char = $format[$i];
            if ($char == '%') {
                $i++;
                $formatted .= self::date_part($format[$i], $time);
            } else {
                $formatted .= $char;
            }
            $i++;
        }
        
        return $formatted;
    }



    public static function currentdate($format = null)
    {
        return self::date(($format !== null) ? $format : ws_config('dateFormat'));
    }

    public static function currenttime($format = null)
    {
        return self::date(($format !== null) ? $format : ws_config('timeFormat'));
    }

    public static function currenttimestamp($format = null)
    {
        return self::date(($format !== null) ? $format : ws_config('timestampFormat'));
    }

    /**
     * <@ADDROWS ARRAY=arrayVarName VALUE=rowsToAdd
     * [POSITION=position] [SCOPE=scope]>
     *
     * Adds the rows specified in VALUE to the array in the variable named by 
     * ARRAY. This tag does not return anything.
     *
     * If the variable specified by the ARRAY attribute does not exist, it is 
     * created.
     *
     * The VALUE attribute specifies the row(s) to add. You may use the 
     * <@VAR> tag and specify a variable containing an array, or specify any 
     * other meta tag that returns an array. This array must have the same 
     * number of columns as the one specified by ARRAY; otherwise, an error is 
     * generated.
     *
     * For single-column arrays, the VALUE attribute may be a text value, rather 
     * than an array. In this case, a single row is added with the value specified.
     *
     * The POSITION attribute specifies the index of the row to start adding 
     * from; the rows are added after the specified row. To add rows to the 
     * beginning of the array, use 0 as the value for POSITION. To add rows to 
     * the end of the array, use -1. If POSITION is not specified, the rows are 
     * added to the end.
     *
     * The SCOPE attribute specifies the scope of the variable specified as the 
     * value of the ARRAY attribute. If the scope is not specified, the default 
     * scoping rules are used. 
     *
     * Meta tags are permitted in any of the attributes.
     */ 
    public static function addrows(&$array, $value, $position = null)
    {
        if (!is_array($array)) {
            $array = array();
        }

        if ($position !== null) {
            $position -= 1;
        }

        $src_dimensions = is_array(@$array[0]) ? 2 : 1;
        $dest_dimensions = is_array(@$value[0]) ? 2 : is_array($value) ? 1 : 0;

        if ($src_dimensions === $dest_dimensions) {
            // TODO: validate that src and dest column counts match.
            if ($position === null) {
                $array = array_merge($array, $value);
            } else {
                array_splice($array, $position, 0, $value);
            }
        } else {
            $array[] = $value;
        }
    }

    /**
     * <@APPFILE [ENCODING=encoding]>
     */
    public static function appfile()
    {
        return $_SERVER['PATH_INFO'];
    }

    /**
     * <@ARRAY [ROWS=rows] [COLS=cols] [VALUE=textValue]
     * [CDELIM=columnDelimString] [RDELIM=rowDelimString]>
     *
     * Returns an array with a specified number of rows and columns.
     *
     * This meta tag is usually used in conjunction with <@ASSIGN>. See the 
     * examples in this section.
     *
     * The attributes ROWS and COLS optionally specify the number of rows and 
     * columns in the array, respectively. The optional attribute VALUE specifies 
     * a string used for initializing the array, formatted as array elements 
     * separated by CDELIM and RDELIM text. 
     *
     * ROWS and COLS must be specified if VALUE is not specified. VALUE must 
     * be specified if ROWS and COLS are not specified. 
     * If all three of these attributes are specified, they must be in accord, or an 
     * error is generated. The following example would generate an error 
     * because the VALUE specifies three columns and two rows, which 
     * contradicts the ROWS and COLS attributes.
     * <@ARRAY ROWS=10 COLS=2 VALUE="a,b,c;d,e,f">
     *
     * It is also invalid to specify a VALUE attribute with different numbers of 
     * columns in each row. The number of columns in each row must be the 
     * same, and must match the COLS value, if specified.
     *
     * If the CDELIM and RDELIM attributes were specified as"," and ";", 
     * respectively, and the value string were specified as 
     * VALUE="1,2,3;4,5,6;7,8,9;a,b,c;" an array with the following 
     * structure would be created:
     *
     * 1 2 3
     * 4 5 6
     * 7 8 9
     * a b c
     * 
     * If no values for the column or row delimiters are specified, then the 
     * values specified by the configuration variables cDelim and rDelim are 
     * used as defaults.
     */
    public static function makearray($rows = null, $cols = null, $value = null, $cdelim = ',', $rdelim = ';')
    {
        if (!strlen($value)) {
            // Build array from ROWS and COLS.
            if ($rows < 1 || $cols < 1) {
                throw new Exception('ws_array: ROWS and COLS cannot be less than 1.');
            }

            return array_fill(0, $rows, array(null));
        } else {
            // Build array from VALUE.
            $array = array();
            $row_list = explode($rdelim, $value);

            if ($rows && $rows != count($row_list)) {
                throw new Exception('ws_array: ROWS must much the number of rows in VALUE.');
            }

            foreach ($rows as $row) {
                $array[] = explode($cdelim, $row);
            }

            // TODO: check column count against actual columns.

            return $array();
        }
    }

    /**
     * <@CGI [ENCODING=encoding]>
     *
     * Returns the full path and name of the Witango CGI. 
     * With server plug-in/extension versions of Witango, this meta tag returns 
     * nothing.
     *
     * Since this is PHP and no cgi path is required, this returns nothing.
     */
    public static function cgi()
    {
        return '';
    }

    /**
     * <@CGIPARAM NAME=name [ENCODING=encoding]>
     *
     * Evaluates to the value of the specified CGI attribute. CGI attributes are 
     * values passed to Witango Server by your Web server. CGI attributes are 
     * passed whether you are using the CGI or the plug-in version of Witango 
     * Server. 
     */
    public static function cgiparam($name)
    {
        switch ($name) {
        case 'CLIENT_ADDRESS':
            // The fully-qualified domain name of the user who called 
            // the application file, if your Web server is set to do DNS 
            // lookups; otherwise, this attribute contains the user’s IP 
            // address. For example, “fred.xyz.com”.
            return $_SERVER['REMOTE_ADDR'];

        case 'CLIENT_IP':
            //The IP address of the user who called the application file. 
            //For example, “205.189.228.30”. 
            return $_SERVER['REMOTE_ADDR'];
            
        case 'CONTENT_TYPE':
            //The MIME type of the HTTP request contents. 
            return $_SERVER['HTTP_ACCEPT'];

        case 'FROM_USER':
            //Rarely returns anything; with some older Web browser 
            //applications, the user’s e-mail address.
            die('NA');

        case 'HTTP_COOKIE':
            //Returns the value of the HTTP cookie specified in the 
            //COOKIE attribute. For example, <@CGIPARAM 
            //NAME="HTTP_COOKIE" COOKIE="SICode"> returns 
            //the value of the SICode cookie. (This attribute is retained 
            //for backwards compatibility with Witango 2.3. It is 
            //recommended that you use <@VAR> with 
            //SCOPE="COOKIE" to return the values of cookies in 
            //Witango. See <@VAR>  on page 320.)
            die('NA');

        case 'HTTP_SEARCH_ARGS':
            //Text after question mark (?) in the URL. 
            return $_SERVER['QUERY_STRING'];

        case 'METHOD':
            //The HTTP request method used for the current request. 
            //If a normal URL call, or form submitted with the GET 
            //method, “GET”; if a form submitted with the POST 
            //method, “POST”. 
            return $_SERVER['REQUEST_METHOD'];
        
        case 'PATH_ARGS':
            //Text after the base URL (which includes the Witango 
            //CGI name, if present), and before any search arguments 
            //in the URL. <@APPFILE> returns the same value if 
            //there is no argument after the application file name and 
            //before any search arguments.
            //For example, in the following two cases:
            //(CGI) http://www.example.com/
            //Witango-bin/wcgi/fred 
            //search.taf?function=_form
            //(plug-in) http://www.example.com/
            //fred/search.taf?function=_form
            //<@CGIPARAM NAME="PATH_ARGS"> returns:
            //    fred/search.taf
            return $_SERVER['PHP_SELF'];

        case 'POST_ARGS':
            //The raw POST (form submission) argument contents, 
            //containing the names and values of all form fields. 
            //
            // See http://us.php.net/manual/en/wrappers.php.php
            return file_get_contents("php://input");

        case 'REFERER':
            //The URL of the Web page from which the current 
            //request was initiated. Not provided by all Web browsers. 
            //(The misspelling of this attribute is for consistency with 
            //the CGI specification.) 
            return $_SERVER['HTTP_REFERER'];

        case 'SCRIPT_NAME':
            //Returns the CGI portion of the URL.
            return $_SERVER['SCRIPT_NAME'];

        case 'SERVER_NAME':
            //Fully-qualified domain name of the Web server, if your 
            //Web server is set to do DNS lookups; otherwise, this 
            //attribute contains the server’s IP address. For example, 
            //“www.example.com”. 
            return $_SERVER['SERVER_NAME'];

        case 'SERVER_PORT':
            //The TCP/IP port on which the Web server is running. A 
            //typical Web server runs on port 80. 
            return $_SERVER['SERVER_PORT'];

        case 'USERNAME':
            //The user name, obtained with HTTP authentication, of 
            //the user who requested the URL. This attribute is 
            //available only if the URL used to call the current 
            //application file required authentication by the Web 
            //server software. 
            return $_SERVER['PHP_AUTH_USER'];

        case 'PASSWORD':
            //The password, obtained with HTTP authentication, of the 
            //user who requested the URL. This attribute is available 
            //only if the URL used to call the current application file 
            //required authentication by the Web server software. 
            return $_SERVER['PHP_AUTH_PW'];

        case 'USER_AGENT':
            //The internal name of the Web browser application being 
            //used to request the URL. This often contains information 
            //about the platform (Mac OS X, Windows, etc.) on which 
            //the Web browser is running, and the application’s 
            //version. 
            return $_SERVER['HTTP_USER_AGENT'];
        }
    }

    /**
     * Handles Witango configuration.
     */
    public static function config($key, $value = null)
    {
        // Comes with default values.
        static $values = [
            'dateFormat' => '%m/%d/%Y',
            'timeFormat' => '%H:%M:%S',
            'timestampFormat' => '%m/%d/%Y %H:%M:%S',
        ];

        if ($value === null) {
            return array_key_exists($key, $values) ? $values[$key] : null;
        } else {
            $values[$key] = $value;
        }
    }

    /**
     * <@CHOICELIST NAME=inputname TYPE=select|radio 
     * OPTIONS=optionsarray [SIZE=size] [MULTIPLE=yes|no] 
     * [CLASS=classname] [STYLE=stylename] [onBlur=script] 
     * [onClick=script] [onFocus=script] [VALUES=valuesarray] 
     * [SELECTED=selectedarray] [SELECTEXTRAS=selectattributes] 
     * [OPTIONEXTRAS=optionattributes] [TABLEEXTRAS=tableattributes] 
     * [TREXTRAS=trattributes] [TDEXTRAS=tdattributes] 
     * [LABELPREFIX=prefix] [LABELSUFFIX=suffix] [COLUMNS=number] 
     * [ROWS=number] [ORDER=columns|rows] [ENCODING=encoding]> 
     *
     * Description <@CHOICELIST> allows you to easily create HTML selection list boxes, 
     * pop-up menus/drop-down lists, and radio button clusters using data from 
     * variables, database values, and so on.
     * This meta tag accepts all the attributes of the standard HTML <SELECT> 
     * tag and of the <INPUT TYPE=radio> tags. It also accepts additional 
     * attributes for specifying the values in the list and the selected item(s). 
     * Radio button groups are always formatted as a table, and an additional 
     * series of attributes defines how the radio button group table is to be 
     * formatted. 
     * The TYPE attribute defines the type of choice list to create. This is one of 
     * SELECT or RADIO (which can be abbreviated as S and R). SELECT is the 
     * default if nothing is specified.
     * 
     * The OPTIONS attribute specifies an array of option names to appear in 
     * the selection list or radio button group. The array may have either a 
     * single column (one option name in each row) or a single row (one option 
     * name in each column). 
     * The VALUES attribute defines an optional array of option values. If 
     * specified, the size of the array must match the one specified in the 
     * OPTIONS attribute. Each array element becomes the value for its 
     * corresponding element in the OPTIONS array. If this attribute is not 
     * specified, the value for each option is the same as its name. 
     * The SELECTED attribute defines a single value or an array of values to be 
     * selected in the list. The value(s) must match items appearing in the 
     * VALUES attribute, if specified, or the OPTIONS attribute if VALUES is not 
     * specified. Items in this array are selected in the displayed selection list or 
     * radio button group. 
     * The OPTIONEXTRAS attribute can be used to set additional <OPTION> 
     * tag attributes or <INPUT TYPE=radio> tag attributes. The value of this 
     * attribute is placed without parsing in the HTML <OPTION> tag or 
     * <INPUT TYPE=radio> tag. For example, 
     * OPTIONEXTRAS='CLASS="fred"' adds the CLASS="fred" attribute 
     * to each <OPTION> tag or <INPUT TYPE=radio> tag.
     * The following attributes apply only to lists:
     *
     * • The SIZE attribute specifies the number of rows in the list that 
     * should be visible at the same time.
     * • The MULTIPLE attribute allows multiple selections. 
     * • The SELECTEXTRAS attribute can be used to set additional 
     * <SELECT> tag attributes. The value of this attribute is placed without 
     * parsing in the HTML <SELECT> tag. For example, 
     * EXTRAS='ID="alpha"' adds the ID="alpha" attribute to the 
     * <SELECT> tag.
     * The following attributes apply only to radio button groups:
     * • The TABLEEXTRAS attribute sets a value that is added to the TABLE 
     * tag for the radio cluster.
     * onFocus The onFocus event occurs when an element receives focus either by 
     * the pointing device or by tabbing navigation. 
     * onClick The onClick event occurs when the pointing device button is clicked 
     * over an element. 
     * STYLE This attribute specifies style information for the current element. 
     * Attribute Definition<@CHOICELIST>
     * 126 126
     * • The TREXTRAS attribute sets a value that is added to each TR tag for 
     * the radio cluster.
     * • The TDEXTRAS attribute sets a value that is added to each TD tag for 
     * the radio cluster.
     * • The LABELPREFIX attribute sets a value that is prefixed to each 
     * radio button label.
     * • The LABELSUFFIX attribute sets a value that is appended to each 
     * radio button label.
     * • The COLUMNS attribute sets the number of columns of radio buttons 
     * in a radio cluster. If COLUMNS is specified, ROWS is ignored. If neither 
     * ROWS nor COLUMNS is specified, then a single-column cluster is 
     * created.
     * • The ROWS attribute sets the number of rows of radio buttons in a 
     * radio cluster. If COLUMNS is specified, this attribute is ignored. 
     * • The ORDER attribute sets the direction in which the options are 
     * displayed. This attribute has two possible values: COLUMNS means 
     * each column (left to right) is filled first; ROWS means each row (top to 
     * bottom) is filled first. COLUMNS is the default value of this attribute. 
     * This attribute is used only if more than one column or row is 
     * generated.
     * 
     * The ENCODING attribute works slightly differently for the 
     * <@CHOICELIST> meta tag: the default encoding for this meta tag is NONE; 
     * that is, no escaping of special characters is done for the result of the meta 
     * tag; however, this tag does do encoding (always) as part of its normal 
     * operation; that is, any special characters within the arrays that define the 
     * options list are escaped for HTML. For example, if you specified a list of 
     * operators in the options list (= [equals];< [less than];> [greater than]), the 
     * characters that have special meaning within HTML (the less-than and 
     * greater-than characters) would be encoded as &lt; and &gt;, which are 
     * special HTML escape sequences. This appears correctly in a Web 
     * browser; that is, as “<” and “>”.
     *
     * @param $name     Input name
     * @param $type     select|radio
     * @param $options  Array of options
     * @param $params   All additional options
     */
    public static function choicelist($name, $options, $values = null, $params = null)
    {
        if (!is_array($options)) {
            $options = [];
        }
        if (!is_array($values)) {
            $values = $options;
        }
        if (!is_array($params)) {
            $params = [];
        }

        $defaults = [
            'type' => 'select', // Values: select|radio
            'size' => null,
            'multiple' => 'no', // Values: yes|no
            'class' => '',
            'style' => '',
            'onblur' => '',
            'onclick' => '',
            'onfocus' => '',
            'selected' => null, // Array of selected items.
            'selectedextras' => '', // String containing extra attributes for select element.
            'optionextras' => '', // String containing exter attributes for option elements.
            'tableextras' => '', // String containing extra attributes for table element (for radio type).
            'trextras' => '', // String containing extra attributes for TR elements (for radio type).
            'tdextras' => '', // String containing extra attributes for TD elements (for radio type).
            'labelprefix' => '', // For radio button labels.
            'labelsuffix' => '', // For radio button labels.
            'columns' => null, // Number of columns of radio buttons in a radio cluster.
            'rows' => null, // Number of rows of radio buttons in a radio cluster.
            'order' => 'columns', // Columns first or rows first.
        ];

        $params = array_merge($defaults, $params);
        $lines = [];
        $selected = [];
        $attrs = [];
        $attrs['name'] = $name;
        if ($params['size']) $attrs['size'] = (int)$params['size'];
        if ($params['multiple'] == 'yes') $attrs['multiple'] = 'multiple';
        if ($params['class'] != '') $attrs['class'] = $params['class'];
        if ($params['style'] != '') $attrs['style'] = $params['style'];
        if ($params['onblur'] != '') $attrs['onblur'] = $params['onblur'];
        if ($params['onclick'] != '') $attrs['onclick'] = $params['onclick'];
        if ($params['onfocus'] != '') $attrs['onfocus'] = $params['onfocus'];
        if ($params['selected'] !== null) $selected = is_array($params['selected']) ? $params['selected'] : [$params['selected']];
        if ($params[''] != '') $attrs[''] = $params[''];

        $lines[] = '<select>';
        for ($i = 0; $i < count($options); $i++) {
            $lines[] = '<option></option>';
        }
        $lines[] = '</select>';

        return implode("\n", $lines);
    }

    private static function render_attributes($attrs)
    {
        $parts = [];
        
    }

    /**
     * <@DATEDIFF DATE1=firstdate DATE2=seconddate [FORMAT=format]>
     *
     * Returns the number of days between the two dates specified.
     *
     * <@DATEDIFF> handles ODBC, ISO, some numeric formats, and textual 
     * formats. 
     * 
     * If the date is entered incorrectly—wrong separators or wrong values for 
     * year, month or day—the tag returns “Invalid date!”.
     *
     * The date attributes are mandatory. If no attribute is found while the 
     * expression is parsed, the tag returns “No attribute!”.
     * 
     * All formats assume the Gregorian calendar. All years must be greater 
     * than zero.
     * 
     * Two-digit year: 00-36 == 2000s, 37-99 == 1900s
     */
    public static function datediff($date1, $date2, $format = '')
    {
        error_log('WitangoLib::datediff - not implemented yet.');
    }

    /**
     * <@KEEP STR=string CHARS=char [ENCODING=encoding]>
     *
     * Returns the string specified in STR stripped of all characters except those 
     * specified in CHARS. The operation of this meta tag is case sensitive. To 
     * retain both upper and lower case variations of a character include both 
     * characters in the CHARS.
     *
     * Each of the attributes to <@KEEP> may include both literal values and 
     * meta tags that return values.
     */
    public static function keep($str, $chars)
    {
        $out = '';
        $char_list = str_split($chars);
        for ($i = 0; $i < strlen($str); $i++) {
            if (in_array($str[$i], $char_list)) {
                $out .= $str[$i];
            }
        }
        return $out;
    }

    /**
     * <@NUMROWS [ARRAY=array]>
     *
     * Returns the number of rows in an action’s result rowset or in the 
     * specified array.
     *
     * Without the ARRAY attribute, this meta tag is valid in the Results HTML 
     * of any results returning action, and returns the number of rows in the 
     * result rowset.
     *
     * With the optional ARRAY attribute, which accepts the name of a variable 
     * containing an array, the tag may be used anywhere that meta tags are 
     * valid, and returns the number of rows in the named array
     * 
     */
    public static function numrows($array = null)
    {
        if ($array === null) {
            throw new Exception('ws_numrows: Implicit array not supported yet');
        } elseif (is_array($array)) {
            return count($array);
        } else {
            return 0;
        }
    }

    /**
     * <@NUMCOLS [ARRAY=array]>
     *
     * Returns the number of columns in each row.
     *
     * Without the ARRAY attribute, this meta tag is valid in the Results HTML 
     * of any results returning action, and returns the number of columns in the 
     * result rowset.
     *
     * With the optional ARRAY attribute, which accepts the name of a variable 
     * containing an array, the tag may be used anywhere meta tags are valid and 
     * returns the number of columns in the named array.
     * 
     */
    public static function numcols($array = array())
    {
        if ($array === null) {
            throw new Exception('ws_numrows: Implicit array not supported yet');
        } elseif (is_array($array)) {
            return @count($array[0]);
        } else {
            return 0;
        }
    }

    /**
     * <@OMIT STR=string CHARS=char [ENCODING=encoding]>
     *
     * Returns the value specified in STR stripped of all characters specified in 
     * CHARS. The operation of this meta tag is case sensitive. To omit both the 
     * upper and lower case variations of a character, you must include both 
     * characters in CHARS.
     *
     * Each of the attributes of <@OMIT> may be specified using a literal value, 
     * meta tags that return values, or a combination of both.
     */ 
    public static function omit($string, $chars)
    {
        return str_replace(str_split($chars), '', $str);
    }

    /**
     * <@SORT ARRAY=arrayVarName [COLS=sortCol [sortType] [sortDir] [, 
     * ...]] [SCOPE=scope]>
     * 
     * Sorts the input array by the column(s) specified. This tag does not return 
     * anything.
     *
     * The ARRAY attribute specifies the name of a variable containing an array. 
     * The COLS attribute specifies the column(s) to sort by, specified using 
     * column numbers or names, with optional sort types (sortType) and 
     * directions (sortDir). 
     *
     * Valid sort types are SMART (the default), DICT, ALPHA and NUM. DICT
     * sorts the column alphabetically, irrespective of case. ALPHA is a casesensitive sort. NUM sorts the column numerically. SMART checks whether 
     * values are numeric or alphabetic and sorts using a NUM or DICT type. 
     *
     * Valid sort directions are ASC (the default) and DESC. ASC sorts the 
     * column in ascending order, with lower values coming before higher ones. 
     * DESC sorts in descending order, with higher values coming before lower 
     * ones.
     *
     * If the COLS attribute is omitted, all columns are sorted left to right using 
     * the SMART sort type and the ASC (ascending) sort direction.
     * The order of the type and direction options are not important, that is, 
     * COLS="1 NUM ASC" is equivalent to COLS="1 ASC NUM".
     *
     * Multiple columns may be specified, separated by commas. Each sort 
     * column specification may include a sort type specifier and/or a sort 
     * direction specifier. If included, these must follow the sort column, 
     * separated by a space.
     *
     * Multiple sort columns cause the array to be sorted by the first column 
     * specified, then, rows with the same value in that column are sorted by 
     * the second sort column specified within that previously-created sort 
     * order, and so on.
     *
     * The SCOPE attribute specifies the scope of the variable specified by 
     * ARRAY. If not specified, the default scoping rules are used.
     *
     * Meta tags are permitted in any of the attributes.
     */
    public static function sort($array, $cols = '')
    {
        error_log('WitangoLib::sort - not implemented yet');
        return $array;
    }

    /**
     * <@VARINFO NAME=variable ATTRIBUTE=attribute [SCOPE=scope]>
     *
     * Returns information about variables and accepts three ATTRIBUTE
     * values, TYPE, ROWS, and COLS:
     * • TYPE returns either text or array. 
     * • ROWS returns the number of rows if the variable is an array, or “0” 
     * otherwise. 
     * • COLS returns the number of columns if the variable is an array, or 
     * “0” otherwise. 
     * • SIZE returns the number of bytes used by the variable or array
     */
    public static function varinfo($var, $attribute)
    {
        switch (strtolower($attribute)) {
        case 'type':
            return is_array($var) ? 'array' : 'text';
        case 'rows':
            return is_array($var) ? count($var) : 0;
        case 'cols':
            return is_array($var) && is_array(@$var[0]) ? count($var[0]) : 0;
        case 'size':
            throw new Exception('ws_varinfo: size attribute not implemented.');
        }
    }

    /**
     * Containment. Returns true if specified string or number is contained in the array
     * Occurrence. Returns true if specified string or number is a substring of the source string.
     */
    public static function contains($haystack, $needle)
    {
        if (is_array($haystack)) {
            return in_array($needle, $haystack);
        } else {
            return false !== strpos($haystack, $needle);
        }
    }

    /**
     * Occurrence. Returns true if specified string or number begins the source string. (Case-insensitive.)
     */
    public static function beginswith($haystack, $needle)
    {
        return strtolower(substr($haystack, 0, strlen($needle))) === strtolower($needle);
    }

    /**
     * Occurrence. Returns true if specified string or number ends the source string. (Case-insensitive.)
     */
    public static function endswith($haystack, $needle)
    {
        return strtolower(substr($haystack, -strlen($needle))) === strtolower($needle);
    }

    public function encoding($value, $encoding)
    {
        error_log('WitangoLib::encoding - not implemented yet.');
        return $value;
    }
}