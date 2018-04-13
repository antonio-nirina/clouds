<?php
namespace AdminBundle\Component\FileUpload;

class DocumentFileBlackList
{
    /*const BLACK_LIST = array(
        'app',
        'exe',
        'php',
        'bin',
        'js',
        'png',
        'bmp',
        'gif',
        'jpeg',
        'tiff',
        'webm',
        'mkv',
        'flv',
        'vob',
        'ogv',
        'ogg',
        'avi',
        'wmv',
        'mp2',
        'mpeg',
        'mpe',
        'mpg',
        'mpv',
        'm4v',
        '3gp',
    );*/

    const BLACK_LIST = array('action', 'apk', 'app', 'bat', 'bin', 'cmd', 'com', 'command', 'cpl', 'csh', 'exe', 'gadget', 'inf', 'ins', 'inx', 'ipa', 'isu', 'job', 'jse', 'ksh', 'lnk', 'msc', 'msi', 'msp', 'mst', 'osx', 'out','paf','pif','prg','ps1','reg','rgs','run','scr','sct','shb','shs','u3p','vb','vbs','vbscript','workflow','ws', 'wsf', 'wsh', 'oxe', '73k', '89k', 'a6p', 'ac', 'acc', 'acr', 'actm', 'ahk', 'air', 'arscript', 'as', 'asb', 'awk', 'azw2', 'beam', 'btm', 'cel', 'celx', 'chm', 'cof', 'crt', 'dek', 'dld', 'dmc', 'docm', 'dotm', 'dxl', 'ear', 'ebm', 'ebs', 'ebs2', 'ecf', 'eham', 'elf', 'es', 'ex4', 'exopc', 'ezs', 'fas', 'fky', 'fpi', 'frs', 'fxp', 'gs', 'ham', 'hms', 'hpf', 'hta', 'iim', 'ipf', 'isp', 'jar', 'js', 'jsx', 'kix', 'lo', 'ls', 'mam', 'mcr', 'mpx', 'mrc', 'ms', 'mxe', 'nexe', 'obs', 'ore', 'otm', 'pex', 'plx', 'potm', 'ppam', 'ppsm','pptm', 'prc', 'pvd', 'pwc', 'pyc', 'pyo', 'qpx', 'rbx', 'rox', 'rpj', 's2a', 'sbs', 'sca', 'scar', 'scb','script', 'smm', 'spr', 'tcp', 'thm', 'tlb', 'tms', 'udf', 'upx',  'url', 'vlx',  'vpm', 'wcm', 'widget', 'wiz','wpk', 'wpm', 'xap', 'xbap', 'xlam', 'xlm', 'xlsm' ,'xltm', 'xqt', 'xys', 'zl9', 'bmp', 'gif', 'jpeg', 'jpg','png',  'tiff', 'webm', 'mkv', 'flv',' vob', 'ogv',  'ogg',  'avi', 'wmv', 'amv', 'mpg', 'mp2', 'mpeg', 'mpe','mpg', 'mpv', 'm4v', '3gp', 'php', 'php5');
}
