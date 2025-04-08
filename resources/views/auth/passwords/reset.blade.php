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

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    @if ($errors->any())
                        <div class="form__errors">
                            <ul style="color: red; margin-top: 1rem; list-style-type: disc; padding-left: 1.5rem;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <h1 class="form__title">Reset Password</h1>

                    <div class="form__div form__div-one">
                        <div class="form__icon">
                            <i class='bx bx-envelope'></i>
                        </div>

                        <div class="form__div-input">
                            <label for="email" class="form__label">Email Address</label>
                            <input id="email" type="email" class="form__input @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>
                            @error('email')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form__div">
                        <div class="form__icon">
                            <i class='bx bx-lock'></i>
                        </div>

                        <div class="form__div-input">
                            <label for="password" class="form__label">New Password</label>
                            <input id="password" type="password" class="form__input @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" oninput="validatePassword()">
                            @error('password')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    

                    <div class="form__div">
                        <div class="form__icon">
                            <i class='bx bx-lock-alt'></i>
                        </div>

                        <div class="form__div-input">
                            <label for="password-confirm" class="form__label">Confirm Password</label>
                            <input id="password-confirm" type="password" class="form__input" name="password_confirmation" required autocomplete="new-password">
                        </div>
                    </div>

                    <div id="password-rules" style="margin-top: 1rem; font-size: 0.875em;">
                        <p id="rule-length" style="color: red;">• At least 5 characters long</p>
                        <p id="rule-uppercase" style="color: red;">• At least one uppercase letter</p>
                        <p id="rule-lowercase" style="color: red;">• At least one lowercase letter</p>
                        <p id="rule-special" style="color: red;">• At least one special character (@#$%&)</p>
                    </div>

                    <button type="submit" class="form__button">
                        Reset Password
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
        <script>
            function validatePassword() {
                const password = document.getElementById('password').value;

                // Validation rules
                const lengthRule = password.length >= 5;
                const uppercaseRule = /[A-Z]/.test(password);
                const lowercaseRule = /[a-z]/.test(password);
                const specialCharRule = /[@#$%&]/.test(password);

                // Update rule colors
                document.getElementById('rule-length').style.color = lengthRule ? 'green' : 'red';
                document.getElementById('rule-uppercase').style.color = uppercaseRule ? 'green' : 'red';
                document.getElementById('rule-lowercase').style.color = lowercaseRule ? 'green' : 'red';
                document.getElementById('rule-special').style.color = specialCharRule ? 'green' : 'red';
            }
        </script>
    </body>
</html>
