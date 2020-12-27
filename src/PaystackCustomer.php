<?php

namespace Starfolksoftware\PaystackSubscription;

class PaystackCustomer {
    public string $email;
    public string $code;

    public function email($email) {
        $this->email = $email;

        return $this;
    }

    public function code($code) {
        $this->code = $code;

        return $this;
    }

    public function create() {
        $fields = [
            'email' => $this->email
        ];

        $fields_string = http_build_query($fields);

        //open connection
        $ch = curl_init();
        
        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, 'https://api.paystack.co/customer');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer ".config('paystack-subscription.secret'),
            "Cache-Control: no-cache",
        ));
        
        //So that curl_exec returns the contents of the cURL; rather than echoing it
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        
        $err = curl_error($ch);
        
        //execute post
        $result = json_decode(curl_exec($ch));
        curl_close($ch);

        return $err ? [] : $result['data'];
    }

    public function find() {
        $curl = curl_init();
  
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.paystack.co/customer/".($this->code ?? $this->email),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer ".config('paystack-subscription.secret'),
                "Cache-Control: no-cache",
            ),
        ));
        
        $result = json_decode(curl_exec($curl));
        $err = curl_error($curl);
        curl_close($curl);
        
        return $err ? [] : $result['data'];
    }

    public function update(array $fields) {
        $url = "https://api.paystack.co/customer/".$this->code;
        $fields_string = http_build_query($fields);
        //open connection
        $ch = curl_init();
        
        //set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer SECRET_KEY",
            "Cache-Control: no-cache",
        ));
        
        //So that curl_exec returns the contents of the cURL; rather than echoing it
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
        
        $err = curl_error($ch);
        
        //execute post
        $result = json_decode(curl_exec($ch));
        curl_close($ch);

        return $err ? [] : $result['data'];
    }
}

