<?php
class Address
{
    // Instance Attributes
    private $name    = array('FIRST'=>"", 'LAST'=>null);
    private $street  = "";
    private $city    = "";
    private $state   = "";
    private $country = "";
    private $zip     = "";


    // Operations

    // name() prototypes
    //   string name()                          returns name in "Last, First" format.
    //                                          If no first name assigned, then return in "Last" format.
    //                                         
    //   void name(string $value)               set object's $name attribute in "Last, First" 
    //                                          or "Last" format.
    //                                         
    //   void name(array $value)                set object's $name attribute in [first, last] format
    //
    //   void name(string $first, string $last) set object's $name attribute
    //   Note: This function should be identical to the one Professor Bettens has
    function name() {
        // string name()
        if( func_num_args() == 0 ) {
            if( empty($this->name['FIRST']) ) return $this->name['LAST'];
            else                              return $this->name['LAST'].', '.$this->name['FIRST']; 
        }
        
        // void name(string $value)
        else if( func_num_args() == 1 ) {
            $value = func_get_arg(0);

            if( is_string($value) ) 
            {
                $value = explode(',', $value); // convert string to array 

                // Echo in here
         
                if ( count($value) >= 2 ) $this->name['FIRST'] = htmlspecialchars(trim($value[1]));
                else                      $this->name['FIRST'] = '';
         
                $this->name['LAST']  = htmlspecialchars(trim($value[0])); 
            }

            else if( is_array ($value) )
            {
                if ( count($value) >= 2 ) $this->name['LAST'] = htmlspecialchars(trim($value[1]));
                else                      $this->name['LAST'] = '';
         
                $this->name['FIRST']  = htmlspecialchars(trim($value[0])); 
            } 
        }

        // void name($first_name, $last_name)
        else if( func_num_args() == 2 ) {
            $this->name['FIRST'] = htmlspecialchars(trim(func_get_arg(0)));
            $this->name['LAST']  = htmlspecialchars(trim(func_get_arg(1))); 
        }
     
        return $this;
    }




    // street() prototypes
    //   string street()                        return value of $street
    //
    //   void street(string $value)             set object's $street attribute to $value
    function street() {
        // string street()
        if( func_num_args() < 1 ) {
            return $this->street; 
        }

        // void street(string $value)
        else {
            $value = func_get_arg(0);
            $this->street = $value;    // BIG NOTE: Do not do '$this->$street', it is wrong
        }

        return $this;
    }




    // city() prototypes
    //   string city()                          return value of $city
    //
    //   void city(string $value)               set object's $city attribute to $value
    function city() {
        // string city()
        if( func_num_args() < 1 ) {
            return $this->city;
        }

        // void city(string $value)
        else {
            $value = func_get_arg(0);
            $this->city = $value;
        }

        return $this;
    }   




    // state() prototypes
    //   string state()                         return value of $state
    //
    //   void state(string $value)              set object's $state attribute to $value
    function state() {
        // string state()
        if( func_num_args() < 1 ) {
            return $this->state; 
        }

        // void state(string $value)
        else {
            $value = func_get_arg(0);
            if (strlen($value) === 2) {
                // Convert to all uppercase if only 2 letters, ex. CA, AK, PA
                $this->state = strtoupper($value);
            }
            else {
                $this->state = $value;
            }
        }

        return $this;
    }




    // country() prototypes
    //   string country()                       return value of $country
    //
    //   void country(string $value)            set object's $country attribute to $value
    function country() {
        // string country()
        if( func_num_args() < 1 ) {
            return $this->country; 
        }

        // void country(string $value)
        else {
            $value = func_get_arg(0);
            $this->country = $value;
        }

        return $this;
    }




    // zip() prototypes
    //   string zip()                           return value of $zip
    //
    //   void zip(string $value)                set object's $zip attribute to $value
    function zip() {
        // string zip()
        if (func_num_args() < 1) {
            return $this->zip;
        }

        else {
            $value = func_get_arg(0);
            $this->zip = $value;
        }

        return $this;
    }




    function __construct($name="", $street="", $city="", $state="", $country="", $zip="") {
        // delegate setting attributes so validation logic is applied
        $this->name($name);
        $this->street($street);
        $this->city($city);
        $this->state($state);
        $this->country($country);
        $this->zip($zip);
    }




    function __toString() {
        return (var_export($this, true));
    }




    // Returns a tab separated value (TSV) string containing the contents of all instance attributes   
    function toTSV() {
        return implode("\t", [$this->name(), $this->street(), $this->city(), $this->state(), $this->country(), $this->zip()]);
    }




    // Sets instance attributes to the contents of a string containing ordered, tab separated values 
    function fromTSV(string $tsvString) {
        // assign each argument a value from the tab delineated string respecting relative positions
        list($name, $street, $city, $state, $country, $zip) = explode("\t", $tsvString);
        $this->name($name);
        $this->street($street);
        $this->city($city);
        $this->state($state);
        $this->country($country);
        $this->zip($zip);
   }
} // end class Address

?>