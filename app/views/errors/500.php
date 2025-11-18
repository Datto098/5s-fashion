<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Lỗi máy chủ | Zone Fashion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #007bff;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --success-color: #28a745;
            --info-color: #17a2b8;
            --dark-color: #343a40;
            --secondary-color: #6c757d;
            --light-color: #f8f9fa;
            --border-radius-standard: 15px;
            --border-radius-button: 50px;
            --shadow-card: 0 5px 15px rgba(0, 0, 0, 0.08);
            --shadow-hover: 0 8px 25px rgba(0, 0, 0, 0.15);
            --transition-standard: all 0.3s ease;
            --danger-gradient: linear-gradient(135deg, var(--danger-color), #b02a37);
        }

        body {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .error-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .error-content {
            background: white;
            border-radius: var(--border-radius-standard);
            box-shadow: var(--shadow-hover);
            padding: 3rem;
            text-align: center;
            max-width: 600px;
            width: 100%;
            position: relative;
            overflow: hidden;
        }

        .error-content::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: var(--danger-gradient);
        }

        .error-icon {
            font-size: 5rem;
            color: var(--danger-color);
            margin-bottom: 2rem;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                opacity: 1;
            }
            50% {
                transform: scale(1.1);
                opacity: 0.8;
            }
        }

        .error-code {
            font-size: 6rem;
            font-weight: 700;
            color: var(--danger-color);
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        .error-title {
            color: var(--dark-color);
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .error-description {
            color: var(--secondary-color);
            margin-bottom: 2.5rem;
            line-height: 1.6;
        }

        .error-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 12px 30px;
            font-weight: 600;
            border-radius: var(--border-radius-button);
            transition: var(--transition-standard);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: var(--danger-gradient);
            border: none;
            color: white;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #b02a37, var(--danger-color));
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(220, 53, 69, 0.3);
            color: white;
        }

        .btn-outline-secondary {
            background: transparent;
            border: 2px solid var(--secondary-color);
            color: var(--secondary-color);
        }

        .btn-outline-secondary:hover {
            background: var(--secondary-color);
            color: white;
            transform: translateY(-2px);
        }

        @media (max-width: 576px) {
            .error-content {
                padding: 2rem;
                margin: 1rem;
            }
            
            .error-code {
                font-size: 4rem;
            }
            
            .error-icon {
                font-size: 3rem;
            }
            
            .error-actions {
                flex-direction: column;
                align-items: center;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-content">
            <div class="error-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="error-code">500</div>
            <h1 class="error-title">Lỗi máy chủ</h1>
            <p class="error-description">
                Đã xảy ra lỗi trên máy chủ. Chúng tôi đang khắc phục sự cố này. 
                Vui lòng thử lại sau ít phút hoặc liên hệ với chúng tôi nếu vấn đề vẫn tiếp tục.
            </p>
            <div class="error-actions">
                <a href="<?= BASE_URL ?>" class="btn btn-primary">
                    <i class="fas fa-home"></i>
                    Về trang chủ
                </a>
                <a href="javascript:history.back()" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Quay lại
                </a>
            </div>
        </div>
    </div>

    <script>
        // Add entrance animation
        document.addEventListener('DOMContentLoaded', function() {
            const errorContent = document.querySelector('.error-content');
            
            errorContent.style.opacity = '0';
            errorContent.style.transform = 'translateY(50px)';
            
            setTimeout(() => {
                errorContent.style.transition = 'all 0.8s ease';
                errorContent.style.opacity = '1';
                errorContent.style.transform = 'translateY(0)';
            }, 100);
        });
    </script>
</body>
</html>