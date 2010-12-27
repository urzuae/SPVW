function startList(){
  if (document.all&&document.getElementById)
  {
    navRoot = document.getElementById("nav");
    for (i=0; i<navRoot.childNodes.length; i++)
    {
      node = navRoot.childNodes[i];
      if (node.nodeName=="LI")
      {
        node.onmouseover=function()
        {
          this.className+=" over";
        }
        node.onmouseout=function()
        {
          this.className=this.className.replace(" over", "");
        }
      }
    }
  }
}

function pop_catalogo(file, title, ext)
{
  if (ext.indexOf('jpg') != -1)
  {
    var generator=window.open('','catalogo','height=800,width=1000,location=no,resizable=yes,scrollbars=yes,navigation=no,titlebar=no,directories=no');
    generator.document.write('<html><head><title>Catálogo - '+title+'</title>');
    generator.document.write('</head><body>');
    generator.document.write('<a href="javascript:self.close()"><img border="0" src="'+file+'"></a>');
    generator.document.write('</body></html>');
    generator.document.close();
  }
  else
  {
    window.open(file,'catalogo','height=800,width=1000,location=no,resizable=yes,scrollbars=yes,navigation=no,titlebar=no,directories=no');
  }
}