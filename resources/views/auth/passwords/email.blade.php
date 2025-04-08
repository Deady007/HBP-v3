<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!-- ===== CSS ===== -->
        <link rel="stylesheet" href="/css/styles.css">

        <!-- ===== BOX ICONS ===== -->
        <link href='https://cdn.jsdelivr.net/npm/boxicons@2.0.5/css/boxicons.min.css' rel='stylesheet'>

        <title>Reset Password</title>  
        <style>
            .shape1, .shape2 {
                position: absolute;
                width: 200px;
                height: 200px;
                border-radius: 50%;
                animation: float 6s ease-in-out infinite;
                z-index: 2;
                background-color: rgba(122, 169, 250, 0.92);
            }
            .shape1 {
                top: -50px;
                left: -50px;
            }
            .shape2 {
                bottom: -50px;
                right: -50px;
                animation-delay: 3s;
            }
            @keyframes float {
                0%, 100% {
                    transform: translateY(0);
                }
                50% {
                    transform: translateY(-20px);
                }
            }
            .form {
                position: relative;
                z-index: 1;
            }
            .error {
                color: red;
                font-size: 0.875em;
                margin-top: 0.25rem;
                position: absolute;
                top: 2.5rem;
                left: 0;
            }
            .form__div-input {
                position: relative;
            }
        </style>
    </head>
    <body>
        <div class="l-form">
            <div class="shape1"></div>
            <div class="shape2"></div>

            <div class="form">
                <img src="/image/72490574_9775039.jpg" alt="" class="form__img" style="width: 700px; height: auto; margin-top: -1rem;">

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <h1 class="form__title">Reset Password</h1>

                    <div class="form__div form__div-one">
                        <div class="form__icon">
                            <i class='bx bx-envelope'></i>
                        </div>

                        <div class="form__div-input">
                            <label for="email" class="form__label">Email Address</label>
                            <input id="email" type="email" class="form__input" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                            @error('email')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    @if (session('status'))
                        <div class="form__status">
                            <p class="text-green-500 text-sm mt-2">{{ session('status') }}</p>
                        </div>
                    @endif

                    <button type="submit" class="form__button">
                        Send Password Reset Link
                    </button>
                    <p class="form__text">
                        <a href="{{ route('login') }}" class="form__button form__button--signup">
                            Log in
                        </a>
                    </p>
                </form>
            </div>
        </div>
        
        <!-- ===== MAIN JS ===== -->
        <script src="/js/main.js"></script>
    </body>
</html>
