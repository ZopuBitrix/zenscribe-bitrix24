<?php
/**
 * ZenScribe - Upload e Instalação Simples
 * Use este arquivo para fazer upload do ZIP e extrair automaticamente
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$uploadDir = __DIR__ . '/';
$maxFileSize = 50 * 1024 * 1024; // 50MB

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ZenScribe - Upload e Instalação</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .logo {
            text-align: center;
            font-size: 3em;
            margin-bottom: 20px;
        }
        .upload-area {
            border: 2px dashed #007bff;
            border-radius: 8px;
            padding: 40px;
            text-align: center;
            margin: 20px 0;
            background: #f8f9fa;
        }
        .upload-area.dragover {
            border-color: #0056b3;
            background: #e7f3ff;
        }
        .btn {
            background: #007bff;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            display: inline-block;
            margin: 10px;
        }
        .btn:hover {
            background: #0056b3;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .progress {
            width: 100%;
            height: 20px;
            background: #e9ecef;
            border-radius: 10px;
            overflow: hidden;
            margin: 10px 0;
        }
        .progress-bar {
            height: 100%;
            background: #007bff;
            width: 0%;
            transition: width 0.3s ease;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">🎯</div>
        <h1 style="text-align: center;">ZenScribe - Upload e Instalação</h1>
        
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['zipfile'])) {
            $uploadedFile = $_FILES['zipfile'];
            
            if ($uploadedFile['error'] === UPLOAD_ERR_OK) {
                $tempPath = $uploadedFile['tmp_name'];
                $fileName = $uploadedFile['name'];
                $fileSize = $uploadedFile['size'];
                
                // Verificar se é um arquivo ZIP
                if (pathinfo($fileName, PATHINFO_EXTENSION) !== 'zip') {
                    echo '<div class="error">❌ Erro: O arquivo deve ser um ZIP.</div>';
                } elseif ($fileSize > $maxFileSize) {
                    echo '<div class="error">❌ Erro: Arquivo muito grande. Máximo 50MB.</div>';
                } else {
                    $zipPath = $uploadDir . $fileName;
                    
                    if (move_uploaded_file($tempPath, $zipPath)) {
                        echo '<div class="success">✅ Upload realizado com sucesso!</div>';
                        
                        // Tentar extrair o ZIP
                        if (class_exists('ZipArchive')) {
                            $zip = new ZipArchive;
                            if ($zip->open($zipPath) === TRUE) {
                                $extractPath = $uploadDir;
                                $zip->extractTo($extractPath);
                                $zip->close();
                                
                                // Remover o arquivo ZIP após extração
                                unlink($zipPath);
                                
                                echo '<div class="success">🎉 Arquivos extraídos com sucesso!</div>';
                                echo '<div style="margin: 20px 0;">';
                                echo '<h3>🔗 Links para testar:</h3>';
                                echo '<p><strong>Teste:</strong> <a href="test.php" target="_blank">test.php</a></p>';
                                echo '<p><strong>Instalação:</strong> <a href="install-simple.php" target="_blank">install-simple.php</a></p>';
                                echo '<p><strong>Dashboard:</strong> <a href="index.php" target="_blank">index.php</a></p>';
                                echo '</div>';
                                
                                // Tentar definir permissões
                                @chmod($uploadDir . 'logs', 0777);
                                @chmod($uploadDir . 'temp', 0777);
                                
                            } else {
                                echo '<div class="error">❌ Erro ao extrair o arquivo ZIP.</div>';
                            }
                        } else {
                            echo '<div class="error">❌ ZipArchive não disponível. Extraia manualmente.</div>';
                        }
                    } else {
                        echo '<div class="error">❌ Erro ao fazer upload do arquivo.</div>';
                    }
                }
            } else {
                echo '<div class="error">❌ Erro no upload: ' . $uploadedFile['error'] . '</div>';
            }
        }
        ?>
        
        <form method="POST" enctype="multipart/form-data" id="uploadForm">
            <div class="upload-area" id="uploadArea">
                <h3>📁 Selecione o arquivo zenscribe-bitrix-app.zip</h3>
                <p>Arraste e solte o arquivo aqui ou clique para selecionar</p>
                <input type="file" name="zipfile" id="fileInput" accept=".zip" style="display: none;" required>
                <button type="button" class="btn" onclick="document.getElementById('fileInput').click()">
                    📁 Selecionar Arquivo
                </button>
            </div>
            
            <div id="fileInfo" style="display: none; margin: 20px 0;">
                <p><strong>Arquivo selecionado:</strong> <span id="fileName"></span></p>
                <div class="progress">
                    <div class="progress-bar" id="progressBar"></div>
                </div>
            </div>
            
            <div style="text-align: center;">
                <button type="submit" class="btn" id="uploadBtn" style="display: none;">
                    🚀 Fazer Upload e Instalar
                </button>
            </div>
        </form>
        
        <div style="margin-top: 40px; text-align: center; font-size: 12px; color: #999;">
            ZenScribe v2.0.0 - Upload e Instalação Automática
        </div>
    </div>
    
    <script>
        const fileInput = document.getElementById('fileInput');
        const uploadArea = document.getElementById('uploadArea');
        const fileInfo = document.getElementById('fileInfo');
        const fileName = document.getElementById('fileName');
        const uploadBtn = document.getElementById('uploadBtn');
        
        // Drag and drop
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });
        
        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });
        
        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                showFileInfo(files[0]);
            }
        });
        
        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                showFileInfo(e.target.files[0]);
            }
        });
        
        function showFileInfo(file) {
            fileName.textContent = file.name;
            fileInfo.style.display = 'block';
            uploadBtn.style.display = 'inline-block';
        }
        
        // Progress simulation
        document.getElementById('uploadForm').addEventListener('submit', () => {
            const progressBar = document.getElementById('progressBar');
            let progress = 0;
            
            const interval = setInterval(() => {
                progress += Math.random() * 30;
                if (progress > 90) progress = 90;
                
                progressBar.style.width = progress + '%';
                
                if (progress >= 90) {
                    clearInterval(interval);
                }
            }, 200);
        });
    </script>
</body>
</html>
