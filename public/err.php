<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Include Error Resolution Guide</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .container {
            max-width: 1000px;
            width: 100%;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin: 20px 0;
        }
        
        header {
            background: #3498db;
            color: white;
            padding: 25px;
            text-align: center;
        }
        
        h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .error-display {
            background: #ffebee;
            color: #c62828;
            padding: 20px;
            font-family: 'Courier New', monospace;
            border-left: 5px solid #c62828;
            margin: 20px;
            border-radius: 5px;
            overflow-x: auto;
        }
        
        .content {
            padding: 25px;
        }
        
        h2 {
            color: #2c3e50;
            margin: 20px 0 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #ecf0f1;
        }
        
        h3 {
            color: #3498db;
            margin: 15px 0 10px;
        }
        
        p {
            line-height: 1.6;
            margin-bottom: 15px;
            color: #34495e;
        }
        
        .solution {
            background: #e8f5e9;
            padding: 20px;
            border-radius: 5px;
            border-left: 5px solid #4caf50;
            margin: 15px 0;
        }
        
        .solution h3 {
            color: #2e7d32;
        }
        
        .code {
            background: #273746;
            color: #ecf0f1;
            padding: 15px;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            margin: 15px 0;
            overflow-x: auto;
        }
        
        .path {
            color: #9b59b6;
            font-weight: bold;
        }
        
        .important {
            background: #fff3e0;
            padding: 20px;
            border-radius: 5px;
            border-left: 5px solid #ff9800;
            margin: 15px 0;
        }
        
        .important h3 {
            color: #ef6c00;
        }
        
        .steps {
            margin-left: 20px;
            margin-bottom: 15px;
        }
        
        .steps li {
            margin-bottom: 10px;
            line-height: 1.6;
        }
        
        .button {
            display: inline-block;
            background: #3498db;
            color: white;
            padding: 12px 25px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            margin: 10px 5px;
            transition: background 0.3s;
        }
        
        .button:hover {
            background: #2980b9;
        }
        
        footer {
            text-align: center;
            padding: 20px;
            color: #7f8c8d;
            font-size: 14px;
        }
        
        @media (max-width: 768px) {
            .container {
                margin: 10px;
                width: calc(100% - 20px);
            }
            
            h1 {
                font-size: 24px;
            }
            
            .content {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>PHP Include Error Resolution Guide</h1>
            <p>How to fix "Failed to open stream" errors in XAMPP</p>
        </header>
        
        <div class="content">
            <h2>Understanding the Error</h2>
            <p>The error message you're seeing indicates that PHP cannot find the file you're trying to include with the <code>include()</code> function.</p>
            
            <div class="error-display">
                Warning: include(..includes/footer.php): Failed to open stream: No such file or directory in C:\xampp\htdocs\svote\public\index.php on line 65
                <br><br>
                Warning: include(): Failed opening '..includes/footer.php' for inclusion (include_path='C:\xampp\php\PEAR') in C:\xampp\htdocs\svote\public\index.php on line 65
            </div>
            
            <h2>What This Error Means</h2>
            <p>PHP is trying to include the file <span class="path">..includes/footer.php</span> but cannot locate it. This typically happens because:</p>
            <ul class="steps">
                <li>The file path is incorrect</li>
                <li>The file doesn't exist at the specified location</li>
                <li>There's a typo in the file path</li>
                <li>File permissions are restricting access</li>
            </ul>
            
            <div class="solution">
                <h3>Solution 1: Check the File Path</h3>
                <p>The most common issue is an incorrect path. Your code is trying to go up one directory level and then look for an "includes" folder.</p>
                
                <div class="code">
                    // Current path being used (incorrect):<br>
                    include('..includes/footer.php');<br><br>
                    
                    // You're missing a slash. It should probably be:<br>
                    include('../includes/footer.php');
                </div>
                
                <p>Note the <code>../</code> which means "go up one directory level" followed by <code>includes/footer.php</code>.</p>
            </div>
            
            <div class="solution">
                <h3>Solution 2: Verify File Existence</h3>
                <p>Check if the file actually exists at the expected location:</p>
                <ul class="steps">
                    <li>Navigate to <span class="path">C:\xampp\htdocs\svote\includes\</span></li>
                    <li>Check if <span class="path">footer.php</span> exists in that folder</li>
                    <li>If not, you might need to create it or adjust your path</li>
                </ul>
            </div>
            
            <div class="solution">
                <h3>Solution 3: Use Absolute Paths</h3>
                <p>For more reliability, consider using absolute paths instead of relative ones:</p>
                
                <div class="code">
                    // Using an absolute path<br>
                    include('C:/xampp/htdocs/svote/includes/footer.php');<br><br>
                    
                    // Or using $_SERVER['DOCUMENT_ROOT']<br>
                    include($_SERVER['DOCUMENT_ROOT'] . '/svote/includes/footer.php');
                </div>
            </div>
            
            <div class="important">
                <h3>Important Notes</h3>
                <p>When working with file paths in PHP:</p>
                <ul class="steps">
                    <li>Use forward slashes (/) for better cross-platform compatibility</li>
                    <li>Double-check your relative paths - they are relative to the current script</li>
                    <li>Consider using the <code>__DIR__</code> magic constant for more predictable paths</li>
                    <li>Always verify file existence before including if possible</li>
                </ul>
            </div>
            
            <h2>Testing Your Fix</h2>
            <p>After making changes, always test your application thoroughly to ensure the include works correctly and no new issues were introduced.</p>
            
            <div class="code">
                // You can test if a file exists before including it<br>
                $filePath = '../includes/footer.php';<br>
                if (file_exists($filePath)) {<br>
                &nbsp;&nbsp;include($filePath);<br>
                } else {<br>
                &nbsp;&nbsp;echo "Error: File not found at " . $filePath;<br>
                }
            </div>
        </div>
        
        <footer>
            <p>PHP Include Error Resolution Guide | For XAMPP Environments</p>
            <p>Remember to always backup your files before making changes to your code.</p>
        </footer>
    </div>
</body>
</html>