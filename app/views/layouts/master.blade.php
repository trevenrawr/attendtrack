<?php
$user = Teacher::find(Session::get('user'));
if (empty($user)) $user = new Teacher;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $title.' - GTP@CU AttendTrack' }}</title>
    <link rel="stylesheet" type="text/css" href="/css/style.css" media="not print" />
    <link rel="stylesheet" type="text/css" href="/css/print.css" media="print" />
    <script async src="/js/main.js"></script>
    <script async src="/js/sorttable.js"></script>
    <link rel="apple-touch-icon" sizes="57x57" href="/apple-touch-icon-57x57.png" />
    <link rel="apple-touch-icon" sizes="114x114" href="/apple-touch-icon-114x114.png" />
    <link rel="apple-touch-icon" sizes="72x72" href="/apple-touch-icon-72x72.png" />
    <link rel="apple-touch-icon" sizes="144x144" href="/apple-touch-icon-144x144.png" />
    <link rel="apple-touch-icon" sizes="60x60" href="/apple-touch-icon-60x60.png" />
    <link rel="apple-touch-icon" sizes="120x120" href="/apple-touch-icon-120x120.png" />
    <link rel="apple-touch-icon" sizes="76x76" href="/apple-touch-icon-76x76.png" />
    <link rel="apple-touch-icon" sizes="152x152" href="/apple-touch-icon-152x152.png" />
    <link rel="icon" type="image/png" href="/favicon-196x196.png" sizes="196x196" />
    <link rel="icon" type="image/png" href="/favicon-160x160.png" sizes="160x160" />
    <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16" />
    <link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32" />
    <meta name="msapplication-TileColor" content="#da532c" />
    <meta name="msapplication-TileImage" content="/mstile-144x144.png" />
</head>
<body>
    <div id="wrapper">
        <div id="skiptocontent">
            <a href="#maincontent">Skip to main content</a>
        </div>
        
        <div id="banner" role="banner">
            <img style="display: none;" alt="Graduate Teacher Program: University of Colorado Boulder" />
            @if ($user->identikey != '')
            <span class="loggedon">
                Hello, {{ $user->name }}
            </span>
            @endif
        </div>
        
        <div id="menu" role="navigation">
            <table>
                <tr>
                    @if ($user->identikey == '')
                    <?php
                    if (Request::is('T/login'))
                        $sel = 'tabHL';
                    else
                        $sel = '';
                    ?>
                    <td>
                        <span class="{{ $sel }}">
                            <a href="/T/login/">Login</a>
                        </span>
                    </td>
                    
                    @elseif ($user->identikey != 'gtp')
                    <?php
                    if (Request::is('T/info*'))
                        $sel = 'tabHL';
                    else
                        $sel = '';
                    ?>
                    <td>
                        <span class="{{ $sel }}">
                            <a href="/T/info/">View Profile</a>
                        </span>
                    </td>
                    @endif
                    
                    @if ($user->permissions('tinfo'))
                    <?php
                    if (Request::is('T/find*'))
                        $sel = 'tabHL';
                    else
                        $sel = '';
                    ?>
                    <td>
                        <span class="{{ $sel }}">
                            <a href="/T/find/">Teacher Search</a>
                        </span>
                    </td>
                    @endif
                    
                    @if ($user->permissions())
                    <?php
                    if (Request::is('WS/list'))
                        $sel = 'tabHL';
                    else
                        $sel = '';
                    ?>
                    <td>
                        <span class="{{ $sel }}">
                            <a href="/WS/list/">Workshop List</a>
                        </span>
                    </td>
                    @endif
                    
                    @if ($user->permissions('reports'))
                    <?php
                    if (Request::is('R/*'))
                        $sel = 'tabHL';
                    else
                        $sel = '';
                    ?>
                    <td>
                        <span class="{{ $sel }}">
                            <a href="/R/list/">Reports</a>
                        </span>
                    </td>
                    @endif
                    
                    <td><a href="/T/logout/">Logout</a></td>
                </tr>
            </table>
        </div>
        
        <div id="page" role="main">
            <a name="maincontent" id="maincontent" href="#" tabindex="-1"></a>
            @if(Session::has('message'))
                <p class="message">{{ Session::get('message') }}</p>
            @endif
            
            @if($errors->any())
                <ul class="errorList">
                    <?php echo implode('', $errors->all('<li class="error">:message</li>')); ?>
                </ul>
            @endif
            
            @yield('content')
        </div>
    </div>
    <div id="footer">
        &copy;2014-2018 Trevor DiMartino, Graduate Teacher Program, CU Boulder
    </div>
</body>
</html>
