<?php

class Param_item
{
    private $id;
	private $serie_unica; //0 no - 1 si
    private $item;


    public function __construct()
    {
        $this->id=null;

		$this->serie_unica = 0;
        $this->item=null;
    }


    public function save($conn,$serie_unica, $item)
    {

        $query="INSERT INTO param_item VALUES (null,'$serie_unica','$item')";
        $result= mysql_query($query, $conn);
        return $result;
    }



    public function update($conn, $id, $serie_unica, $item)
    {
       
        $query = "UPDATE param_item SET   serie_unica = '$serie_unica', item = '$item'
                  WHERE id = '$id'";
        $result = mysql_query($query, $conn);

        return $result;

    }



    public function get_id($conn, $id)
    {

        $query="SELECT  serie_unica, item FROM iva WHERE id ='$id' AND borrado = 0";
        $result = mysql_query($query, $conn);
        $row = mysql_fetch_assoc($result);
        return $row;
    }
     public function get_iva_borrado_id($conn, $id)
    {

        $query="SELECT   porcentaje, activo FROM iva WHERE id ='$id' AND borrado = 1";
        $result = mysql_query($query, $conn);
        $row = mysql_fetch_assoc($result);
        return $row;
    }

}
?>