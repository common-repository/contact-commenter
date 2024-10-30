<?php

require_once '../../../wp-load.php';

?>

var rpPel = null;
var rpSrc = null;

function $s(){
	if(arguments.length == 1)
		return get$(arguments[0]);
	
	var elements = [];
	$c(arguments).each(function(el){elements.push(get$(el));});

	return elements;
}

function get$(el){
	if(typeof el == 'string')
		el = document.getElementById(el);
	return el;
}

function $c(array){
	var nArray = [];
	for (i=0;el=array[i];i++) nArray.push(el);
	return nArray;
}

function cc_private_reply(event, author_email){
	if(event == null)
		event = window.event;

	rpSrc = event.srcElement? event.srcElement : event.target;
	rpSrc = rpSrc.parentNode;
	rpPel = rpSrc.parentNode;

	var cfm = $s('privatereply');
	if(cfm == null){
		return false;
	}
	
	if(author_email == "" || author_email == null){
		return false;
	}
	
	$s("ccp_reply_email").value = author_email;

	rpPel.insertBefore(cfm,rpSrc.nextSibling);
	cfm.style.display = 'block';

}

function cc_getXMLInstance(){
	var req;
	if(window.XMLHttpRequest){
		req = new XMLHttpRequest();
		if (req.overrideMimeType){
			//req.overrideMimeType('text/xml');
		}
	}else if(window.ActiveXObject){
		try{
			req = new ActiveXObject("Msxml2.XMLHTTP");
		}catch(e){
			try{
				req = new ActiveXObject("Microsoft.XMLHTTP");
			}catch(e){}
		}
	}
	if(!req){
		alert('Giving up :( Cannot create an XMLHTTP instance');
		return false;
	}
	return req;
}

function cc_private_send(){
	var req = cc_getXMLInstance();
	var c = null;

	var comment = null;
	
	var title = null;
	
	title = $s('ccp_title').value;
	comment = $s('ccp_comment').value;

	if(title == null || title == ""){
		alert("<?php _e('Email title can not be empty', 'contact-commenter'); ?>");
		$s("ccp_title").focus();
		return false;
	}
	
	if(comment == null || comment == ""){
		alert("<?php _e('Body can not be empty', 'contact-commenter'); ?>");
		$s("ccp_comment").focus();
		return false;
	}

	var cfm = $s('privatereply');

	var p= "title="+encodeURIComponent(title)+"&body="+encodeURIComponent(comment)+"&author_email="+encodeURIComponent($s("ccp_reply_email").value);

	var dateObj = new Date();

	c = document.createElement('div');
	rpPel.insertBefore(c, rpSrc.nextSibling);


	c.innerHTML = "<div" + " style=\"border:1px dashed red;margin:5px;padding:5px;\" ><p><?php _e('Sending email ...', 'contact-commenter'); ?></p></div>";

	cfm.style.display='none';
	
	req.onreadystatechange = function(){
		if(req.readyState == 4){	
			if(req.status == 200 && req.responseText == 'OK'){
				c.innerHTML = "<div" + " style=\"border:1px dashed green;margin:5px;padding:5px;\" ><p><?php _e('Email Sent Successfully.', 'contact-commenter'); ?></p></div>";
				$s('ccp_reply_email').value = '';
				$s("ccp_comment").value = '';
				$s("ccp_title").value = '<?php _e('Email Title', 'contact-commenter'); ?>';
			}else{
				c.parentNode.removeChild(c);
				var error = req.responseText.match(/<body>[\s\S]*?<p>([\s\S]*)<\/p>[\s\S]*?<\/body>/i);
				if(typeof(error) != 'undefined' && error != null && error != ''){
					alert(error[1]);
				}else{
					alert('<?php _e('Error in sendeing email.', 'contact-commenter'); ?>');
				}
				cfm.style.display='block';
			}
		}
		return false;
	}
	
	req.open('POST', cfm.action, true);
	req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	req.setRequestHeader("Content-length", p.length);
	req.setRequestHeader("Connection", "close");
	req.send(p);
	
	return false;
}
<?php
	die();exit();
?>