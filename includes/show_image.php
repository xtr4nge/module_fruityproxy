<html>
<head> 
  <TITLE>FruityProxy</TITLE> 
  <script language='javascript'>
    //REF: https://www.sitepoint.com/resize-popup-fit-images-size/
    var arrTemp=self.location.href.split("?"); 
    var picUrl = (arrTemp.length>0)?arrTemp[1]:""; 
    var NS = (navigator.appName=="Netscape")?true:false; 
      function FitImage() { 
        iWidth = (NS)?window.innerWidth:document.body.clientWidth; 
        iHeight = (NS)?window.innerHeight:document.body.clientHeight; 
        iWidth = document.images[0].width - iWidth; 
        iHeight = document.images[0].height - iHeight; 
        window.resizeBy(iWidth, iHeight); 
        self.focus(); 
      }; 
  </script> 
</head> 
<body bgcolor="#000000" onload='FitImage();' topmargin="0" marginheight="0" leftmargin="0" marginwidth="0"> 
    <script language='javascript'> 
        document.write( "<img src='" + picUrl + "' border=0>" ); 
    </script> 
</body> 
</html>