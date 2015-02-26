<?php

/* Payment gateway system works as follows
        * Loop while isPaymentCompleted == false
          * processPage: Process current page submission (assuming we have submission, which we can always check for)
          * (Probably check after processing if we are finished)
          * getPageContents: Get the page contents to display, this can be the page we just displayed or the next one in the sequence. This function will allow us to display errors for example by showing the same page again with the errors on it
        * Assuming payment is successful, we then query plugin for any payment details to store with the payment
        * We then store the payment, having created the user account, we then check with the plugin if we can display the user details

*/
// Base class for PaymentGatewayPlugin

class PaymentGatewayPlugin
{

        // Class for initiating single instance and setting up class
        protected $voucher = array(); // Array of selected voucher and details
        protected $useraccount = array(); // Array containing created user details
        
        protected $displayUserPassword = true; // If true we can display user details at the end
        protected $state = array(); // Anything in the state array will be stored between page views for carrying the state between pages
        
        public function __construct($account, $voucher)
        {
            // Set details of user account we are purchasing so plugin case use it (e.g. sms details)
            $this->voucher = $voucher;
            $this->useraccount = $account;
        }
        
        // Function for getting different pages of content from plugin
        public function getPageContents($page = '')
        {
        
                return 'No page contents for this plugin have been defined';
        }
        
        // Function for processing plugin pages, we tell it which page we are on, and it process the page and then tells us which page to goto next (getPageContents function can be used to put in error message and such by making us display the same page again)
        
        public function processPage($page = '')
        {
                return $this->currentPage();
        }
        
        
        // payment_details returns details to be stored along in the payment gateway
        public function getPaymentDetails()
        {
                return 'No Payment details: '. date('c');
        }
        
        // Function that lets us know when the payment has completed successfully
        public function isPaymentCompleted()
        {
                return false;
        }
        
        // Function that tells us if we can display user password
        public function canDisplayPassword()
        {
                return $this->displayUserPassword;
        }
        
        // Function get get state
        public function getState()
        {
                $this->state['savedstate'] = true;
                return $this->state;
        }
        // Function to load state
        public function setState($state)
        {
                $this->state = $state;
        }
        
        public function currentPage()
        {
                return $this->state['currentpage'] ? $this->state['currentpage'] : 'default';
        }
        
        protected function setCurrentPage($page)
        {
                $this->state['currentpage'] = $page;
        }
}
