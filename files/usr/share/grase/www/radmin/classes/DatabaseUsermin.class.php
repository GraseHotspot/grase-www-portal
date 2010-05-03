<?

class DatabaseUsermin
{
    private $db;
    
    private $users = array();

    public function __construct($db)
    {
        $this->db =& $db;
        $this->loadusers();
    }
    
    private function loadusers()
    {
        $sql = "SELECT UserName, Value FROM radcheck WHERE Attribute = 'password'";
                
        $res =& $this->db->query($sql);
        
        // Always check that result is not an error
        if (PEAR::isError($res)) {
            die($res->getMessage());
        }
        
        $results = $res->fetchAll(MDB2_FETCHMODE_ASSOC, false, false);
        
        foreach($results as $user)
        {
            $users[$user['username']] = $user['value'];
        }
                
        $this->users = $users;        
    }
    
    public function getUsers()
    {
        
        return $this->users;
    }
}
