<?php
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Easy set variables
	 */

	/* Array of database columns which should be read and sent back to DataTables */
	$aColumns = array('id_cliente', 'nombre', 'ci_ruc','codigo_tipocliente');
        $aColumnsAux = array('a.nombre', 'a.ci_ruc');
	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "id_cliente";

	/* Database connection */
        include_once '../conexion/conexion.php';
        $usuario = new ServidorBaseDatos();
        $conn = $usuario->getConexion();


        


	/*
	 * Paging
	 */
	$sLimit = "";
	if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
	{
		$sLimit = "LIMIT ".mysql_real_escape_string( $_GET['iDisplayStart'] ).", ".
			mysql_real_escape_string( $_GET['iDisplayLength'] );
	}


	/*
	 * Ordering
	 */
	if ( isset( $_GET['iSortCol_0'] ) )
	{
		$sOrder = "ORDER BY  ";
		for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
		{
			$sOrder .= $aColumnsAux[ intval( $_GET['iSortCol_'.$i] ) ]."
			 	".mysql_real_escape_string( $_GET['sSortDir_'.$i] ) .", ";
		}
		$sOrder = substr_replace( $sOrder, "", -2 );
	}


	/*
	 * Filtering
	 * NOTE this does not match the built-in DataTables filtering which does it
	 * word by word on any field. It's possible to do here, but concerned about efficiency
	 * on very large tables, and MySQL's regex functionality is very limited
	 */
	$sWhere = "";
	if ( $_GET['sSearch'] != "" )
	{
		$sWhere = "AND( ";
		for ( $i=0 ; $i<count($aColumnsAux) ; $i++ )
		{
			$sWhere .= $aColumnsAux[$i]." LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' OR ";
		}

		$sWhere = substr_replace( $sWhere, ")", -3 );
	}


	/*
	 * SQL queries
	 * Get data to display
	 */        
	$sQuery = "
		SELECT SQL_CALC_FOUND_ROWS a.id_cliente as id_cliente, a.nombre as nombre, a.ci_ruc as ci_ruc, a.codigo_tipocliente as codigo_tipocliente
		FROM   cliente a WHERE (a.borrado = 0)
                $sWhere
		$sOrder
		$sLimit
	";
	//$rResult = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());
        $rResult = mysql_query( $sQuery, $conn ) or die(mysql_error());
	/* Data set length after filtering */
	$sQuery = "
		SELECT FOUND_ROWS()
	";
	//$rResultFilterTotal = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());
        $rResultFilterTotal = mysql_query( $sQuery, $conn ) or die(mysql_error());
	$aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
	$iFilteredTotal = $aResultFilterTotal[0];

	/* Total data set length */
	$sQuery = "
		SELECT COUNT(".$sIndexColumn.")
		FROM   cliente
                WHERE borrado = 0
	";
	//$rResultTotal = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());
        $rResultTotal = mysql_query( $sQuery, $conn ) or die(mysql_error());
	$aResultTotal = mysql_fetch_array($rResultTotal);
	$iTotal = $aResultTotal[0];


	/*
	 * Output
	 */
	$sOutput = '{';
	$sOutput .= '"sEcho": '.intval($_GET['sEcho']).', ';
	$sOutput .= '"iTotalRecords": '.$iTotal.', ';
	$sOutput .= '"iTotalDisplayRecords": '.$iFilteredTotal.', ';
	$sOutput .= '"aaData": [ ';
	while ( $aRow = mysql_fetch_array( $rResult ) )
	{
		$sOutput .= "[";
//		for ( $i=0 ; $i<count($aColumns) ; $i++ )
//		{
//			if ( $aColumns[$i] == "id_cliente" )
//			{
//                           $code_aux= $aRow[ $aColumns[$i] ];
//				/* Special output formatting for 'version' */
//				//$sOutput .= ($aRow[ $aColumns[$i] ]=="id_cliente") ?
//					//'"-",' :
//					//'"'.str_replace('"', '\"', $aRow[ $aColumns[$i] ]).'",';
//			}
//			else
//			{
//                            if ( $aColumns[$i] == "nombre" )
//                            {
//                               $nombre_aux = $aRow[ $aColumns[$i] ];
//
//                               $sOutput .= '"'.str_replace('"', '\"', $aRow[ $aColumns[$i] ]).'",';
//
//                            }
//                            else
//                            {
//                                if ( $aColumns[$i] == "ci_ruc" )
//                                {
//                                   $ciruc_aux= $aRow[ $aColumns[$i] ];
//                                   $sOutput .= '"'.str_replace('"', '\"', $aRow[ $aColumns[$i] ]).'",';
//
//                                }
//                                else
//                                {
//
//                                    if ( $aColumns[$i] == "nombret" )
//                                    {
//                                       $nombret_aux= $aRow[ $aColumns[$i] ];
//                                       $sOutput .= '"'.str_replace('"', '\"', $aRow[ $aColumns[$i] ]).'",';
//
//                                    }
//                                    else
//                                    {
//				/* General output */
//                                    $sOutput .= '"'.str_replace('"', '\"', $aRow[ $aColumns[$i] ]).'",';
//                                    }
//                                }
//                            }
//			}
//		}

		/*
		 * Optional Configuration:
		 * If you need to add any extra columns (add/edit/delete etc) to the table, that aren't in the
		 * database - you can do it here
		 */

                $code_aux= $aRow["id_cliente"];
                $nombre_aux = $aRow["nombre"];
                $ciruc_aux= $aRow["ci_ruc"];
                $tipo_aux= $aRow["codigo_tipocliente"];
                
               
               
               
                $cadena="";
                
                $queryFacturas = "SELECT id_factura, codigo_factura, totalfactura FROM facturas WHERE estado = 0 AND anulado = 0 AND id_cliente = '$code_aux'";
                $resultFacturas = mysql_query( $queryFacturas, $conn ) or die(mysql_error());
                
                while ( $aF = mysql_fetch_array( $resultFacturas ) )
                {
                    $debe = 1;
                    
                    $idFactura= $aF["id_factura"];
                    $codigoFactura = $aF["codigo_factura"];
                    $totalFactura = $aF["totalfactura"];
                    
                    $sel_cobros="SELECT sum(importe) as aportaciones FROM cobros WHERE id_factura='$idFactura'";
                    $rs_cobros=mysql_query($sel_cobros,$conn);
                    if($rs_cobros){
                        $aportaciones=mysql_result($rs_cobros,0,"aportaciones");
                    }
                    else{
                        $aportaciones=0;
                    }
                    
                    $pendiente=$totalFactura-$aportaciones;
                    
 
                   
                    $cadena =$cadena . "#Fact: ".$codigoFactura."- Total: ".$totalFactura."- Pendiente: ".$pendiente."*";
                    
                }
                
                
                $sOutput .= '"'.str_replace('"', '\"', "<a href='#' style='font-size: 11px; text-decoration: none' onClick='pon_prefijo($code_aux,&#39;$nombre_aux&#39;,&#39;$ciruc_aux&#39;,&#39;$tipo_aux&#39;,&#39;$cadena&#39;)'>".$aRow["nombre"]."</a>").'",';
                $sOutput .= '"'.str_replace('"', '\"', "<a href='#' style='font-size: 11px; text-decoration: none' onClick='pon_prefijo($code_aux,&#39;$nombre_aux&#39;,&#39;$ciruc_aux&#39;,&#39;$tipo_aux&#39;,&#39;$cadena&#39;)'>".$aRow["ci_ruc"]."</a>").'",';
                
                $sOutput .= '"'.str_replace('"', '\"', "<a href='#'><img src='../img/seleccionar.gif' border='0' width='16' height='16' border='1' title='Seleccionar' onClick='pon_prefijo($code_aux,&#39;$nombre_aux&#39;,&#39;$ciruc_aux&#39;,&#39;$tipo_aux&#39;,&#39;$cadena&#39;)' onMouseOver='style.cursor=cursor'></a>").'",';

                
		$sOutput = substr_replace( $sOutput, "", -1 );
		$sOutput .= "],";
	}
	$sOutput = substr_replace( $sOutput, "", -1 );
	$sOutput .= '] }';

	echo $sOutput;
?>