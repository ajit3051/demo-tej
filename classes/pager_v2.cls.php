<?php


/*--------------------------------------------------+
| CLASS: pager                                      |
| FILE: pager.cls.php                               |
+===================================================+
| Class to generate page links easily               |
+---------------------------------------------------+
| Copyright � 2005 Davis 'X-ZERO' John              |
| Scriptlance ID: davisx0                           |
| Email: davisx0@gmail.com                          |
+---------------------------[ Thu, Jun 16, 2005 ]--*/




define("PAGER_DEF_PAGELIMIT_V2", 5);


class pager_v2
{
	var $urlformat;
	var $total;
	var $perpage;
	var $totalpages;
	var $curpage;


	function __construct($urlformat="", $total=0, $perpage=0, $curpage=0)
	{
		$this->urlformat = $urlformat;
		$this->total = $total;
		$this->perpage = $perpage;
		$this->curpage = $curpage;
		$this->totalpages = ceil($total/$perpage);
	}


	function pagelink($page)
	{
		$link = str_replace("{@PAGE}", $page, $this->urlformat);
		$link = str_replace("{@PERPAGE}", $this->perpage, $link);
		return $link;
	}


	function nextpage()
	{
		if ($this->totalpages && $this->curpage != $this->totalpages)
		{
			return $this->curpage+1;
		}
	}
	
	
	function nextlink()
	{
		if ($this->curpage != $this->totalpages)
		{
			$link = str_replace("{@PAGE}", $this->curpage+1, $this->urlformat);
			$link = str_replace("{@PERPAGE}", $this->perpage, $link);
			return $link;
		}
	}


	function prevpage()
	{
		if ($this->curpage != 1)
		{
			return $this->curpage-1;
		}
	}
	
	
	function prevlink()
	{
		if ($this->curpage != 1)
		{
			$link = $this->urlformat;
			$link = str_replace("{@PAGE}", $this->curpage-1, $link);
			$link = str_replace("{@PERPAGE}", $this->perpage, $link);
			return $link;
		}
	}


	function firstlink()
	{
		if ($this->totalpages != 0)
		{
			$link = $this->urlformat;
			$link = str_replace("{@PAGE}", 1, $link);
			$link = str_replace("{@PERPAGE}", $this->perpage, $link);
			return $link;
		}
	}


	function lastlink()
	{
		if ($this->totalpages != 0)
		{
			$link = $this->urlformat;
			$link = str_replace("{@PAGE}", $this->totalpages, $link);
			$link = str_replace("{@PERPAGE}", $this->perpage, $link);
			return $link;
		}
	}
	
	
	
	function getlinks($limit = PAGER_DEF_PAGELIMIT_V2, $firstandlast = TRUE)
	{
		if ($this->curpage <= $limit+1)
		{
			$start = 1;
			$ellipse1 = "";
			$extra = $limit - ($this->curpage-1);
		}
		else
		{
			$start = $this->curpage - $limit;
			$ellipse1 = "<td class=\"pagetable_ellipses\">...</td>";
			$extra = 0;
		}

		if ($this->totalpages-$this->curpage <= $limit+$extra)
		{
			$end = $this->totalpages;
			$ellipse2 = "";
			$extra = $limit + $extra - ($this->totalpages-$this->curpage);
		}
		else
		{
			$end = $this->curpage + ($limit+$extra);
			$ellipse2 = "<td class=\"pagetable_ellipses\">...</td>";
			$extra = 0;
		}

		if ($extra > 0)
		{
			if ($start>$extra)
			{
				$start = $start-$extra;
				$extra = 0;
			}
			else
			{
				$extra -= $start-1;
				$start = 1;
				$ellipse1 = "";
			}
		}
		

		$links = "<ul class='pagination pull-right pagination-no-margin'>";

		if ($this->totalpages && $firstandlast)
			$links .= "<li class='page-item'><a class='page-link page-link-v2' href=\"".$this->firstlink()."\"  class=\"pagelink_first\">First</a> </li>";
		
		if ($this->prevpage())
			$links .= "<li class='page-item'><a class='page-link page-link-v2' href=\"".$this->prevlink()."\" class=\"pagelink_prev\">&#8249; Prev</a></li>";
		
		$links .= $ellipse1;

		for ($i=$start; $i<$this->curpage; $i++)
			$links .= "<li class='page-item'><a class='page-link page-link-v2' href=\"".$this->pagelink($i)."\" class=\"pagelink\">&nbsp;".$i."&nbsp;</a></li>";

		$links .= "<li class='page-item page_active'><a class='page-link page-link-v2'>".$this->curpage."</a></li>";

		for ($i=$this->curpage+1; $i<=$end; $i++)
			$links .= "<li class='page-item'><a class='page-link page-link-v2' href=\"".$this->pagelink($i)."\" class=\"pagelink\">&nbsp;".$i."&nbsp;</a></li>";

		$links .= $ellipse2;

		if ($this->nextpage())
			$links .= "<li class='page-item'><a class='page-link page-link-v2' href=\"".$this->nextlink()."\" class=\"pagelink_next\">Next &#8250;</a> </li>";

		if ($this->totalpages && $firstandlast)
			$links .= "<li class='page-item'><a class='page-link page-link-v2' href=\"".$this->lastlink()."\" class=\"pagelink_last\">Last</a></li>";

		$links .= "</ul>";

		return $links;

	}


	function outputlinks()
	{
		return $this->getlinks();
	}
}

?>