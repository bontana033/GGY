<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Spot_m extends CI_Model
{
    function __construct()
    {
        parent::__construct();
	}
	/*
	* s_word : 검색어
	* opt : 검색시 검색 분류(제목 + 내용, 제목 등)
	* category : 카테고리(attraction, stay, food, cafe)
	* subcategory : 하위 카테고리(hotel, guesthouse, beach 등)
	*/ 
    function get_list($table='spot', $type='', $offset='', $limit='', $s_word='', $category='', $subcategory='')
    {
		// category word
		$catword='';
		if($category != ''){
			$catword = ' AND category = \''.$category.'\''; 
			if($subcategory != ''){
				$catword = $catword.' AND subcategory = \''.$subcategory.'\'';	
			}
		}

		$opt = 'title';
		$sword= ' WHERE 1=1 ';
		if(isset($_GET['s_word']))	$s_word = $_GET['s_word'];
		if(isset($_GET['opt']))	$opt = $_GET['opt'];
		if ( $s_word != '' )
     	{
			if(strpos($opt, 'title') !== false)	$sword = $sword.' AND title like "%'.$s_word.'%" ';
			if (strpos($opt, 'content') != false)	$sword = $sword.' AND content like "%'.$s_word.'%" ';
     	}

    	$limit_query = '';
    	if ( $limit != '' OR $offset != '' )
     	{
     		//페이징이 있을 경우의 처리
     		$limit_query = ' LIMIT '.$offset.', '.$limit;
		 }
		 

		$sql = "SELECT * FROM ".$table.$sword.$catword.$limit_query;
		$query = $this->db->query($sql);
		$result = $query->result();
		return $result;
    }

    function get_view($table, $id)		
    {	
    	//조회수 증가
		$sql0 = "UPDATE ".$table." SET hits=hits+1 WHERE id=".$id;
   		$this->db->query($sql0);

    	$sql = "SELECT * FROM ".$table." WHERE id=".$id;
   		$query = $this->db->query($sql);

     	//게시물 내용 반환
		$result = $query->row();
    	return $result;
	}
	// categorize_page 상단 카테고리 가져오기 
	function get_category($table)
    {
		$sql = "SELECT distinct category FROM ".$table;
   		$query = $this->db->query($sql);
    	return $query->result();
	}
	// categorize_page 상단 카테고리의 하위 카테고리 가져오기
	function get_subcategory($table, $category='')
    {
		$category_word = '';
		if($category != ''){
			$category_word = " WHERE CATEGORY='".$category."'";
		}
		else{
			return null;
		}
		$sql = "SELECT * FROM ".$table.$category_word;
		$query = $this->db->query($sql);
		return $query->result();
	}
	function check($table, $id, $ip){
		$sql = "SELECT `like` FROM `".$table."` WHERE ip = '".$ip."' AND spot_id =".$id;
		$query = $this->db->query($sql);
		if($query->result() == null){
			$sql_t = "INSERT INTO `LIKE`(spot_id, ip) VALUES($id, '$ip')"; 
			$query = $this->db->query($sql_t);
		}
		$query = $this->db->query($sql);
		return $query->row()->like;
	}
	function toggle_like($table, $id, $ip){
		$check = $this->check($table, $id, $ip);
		$private_like = 1 - $check;
		$spot_like = $check ? -1 : 1;
		$sql = "UPDATE spot set `like` = `like` + ".$spot_like." WHERE id = ".$id;
		$query = $this->db->query($sql);
		$sql = "UPDATE `LIKE` set `like` = ".$private_like." WHERE spot_id = ".$id." AND ip = '".$ip."'";
		$query = $this->db->query($sql);
	}
}