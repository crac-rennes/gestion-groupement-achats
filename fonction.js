
function newWindow(newContent,flag) 
{ 
if (flag)
	winContent = window.open(newContent, 'aide','toolbar=no,status=no,width=300, height=400,scrollbars=no,location=no,resize=yes,menubar=no,titlebar=no,top=200,left=400');
else
	winContent = window.open(newContent, 'aide_responsables','toolbar=no,status=no,width=500, height=600,scrollbars=yes,location=no,resize=yes,menubar=no,titlebar=no,top=100,left=100');
 winContent.focus();
} 
