Set WinScriptHost = CreateObject("WScript.Shell")
WinScriptHost.Run Chr(34) & "C:\inetpub\wwwroot\eproc_pengadaan\script.bat" & Chr(34), 0
Set WinScriptHost = Nothing