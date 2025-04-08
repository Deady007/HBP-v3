<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!-- ===== CSS ===== -->
        <link rel="stylesheet" href="/css/styles.css">

        <!-- ===== BOX ICONS ===== -->
        <link href='https://cdn.jsdelivr.net/npm/boxicons@2.0.5/css/boxicons.min.css' rel='stylesheet'>

        <!-- jQuery UI CSS -->
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

        <!-- Flatpickr CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

        <title>Registration Page</title>  
        <style>
            .shape1, .shape2 {
                position: absolute;
                width: 200px;
                height: 200px;
                border-radius: 50%;
                z-index: 2; /* Bring shapes to the front */
                animation: float 6s ease-in-out infinite;
                background-color:rgba(122, 169, 250, 0.92); /* Change to desired color */
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
            .form__row {
                display: flex;
                justify-content: space-between;
                gap: 1rem;
            }
            .form__row .form__div {
                flex: 1;
            }
            .form__button {
                width: 100%;
                padding: 1rem;
                font-size: var(--normal-font-size);
                outline: none;
                border: none;
                margin-bottom: -1rem;
                background-color: var(--first-color);
                color: #fff;
                border-radius: .5rem;
                cursor: pointer;
                transition: .3s;
            }
            
        </style>
    </head>
    <body>
        <div class="l-form">
            <div class="shape1"></div>
            <div class="shape2"></div>

            <div class="form">
                <img src="/image/IMG_1963.png" alt="" class="form__img">

                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <h1 class="form__title">Register</h1>

                    <div class="form__row">
                        <div class="form__div form__div-one">
                            <div class="form__icon">
                                <i class='bx bx-user-circle'></i>
                            </div>
                            <div class="form__div-input">
                                <label for="name" class="form__label">Name</label>
                                <input id="name" type="text" class="form__input @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form__div">
                            <div class="form__icon">
                                <i class='bx bx-calendar'></i>
                            </div>
                            <div class="form__div-input">
                                <label for="date_of_birth" class="form__label">Date of Birth</label>
                                <input id="date_of_birth" type="text" class="form__input @error('date_of_birth') is-invalid @enderror" name="date_of_birth" value="{{ old('date_of_birth') }}" required autocomplete="date_of_birth">
                                @error('date_of_birth')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form__row">
                        <div class="form__div">
                            <div class="form__icon">
                                <i class='bx bx-phone'></i>
                            </div>
                            <div class="form__div-input">
                                <label for="phone_number" class="form__label">Phone Number</label>
                                <input id="phone_number" type="text" class="form__input @error('phone_number') is-invalid @enderror" name="phone_number" value="{{ old('phone_number') }}" required autocomplete="phone_number">
                                @error('phone_number')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form__div">
                            <div class="form__icon">
                                <i class='bx bx-envelope'></i>
                            </div>
                            <div class="form__div-input">
                                <label for="email" class="form__label">Email</label>
                                <input id="email" type="email" class="form__input @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form__row">
                        <div class="form__div">
                            <div class="form__icon">
                                <i class='bx bx-lock'></i>
                            </div>
                            <div class="form__div-input">
                                <label for="password" class="form__label">Password</label>
                                <input id="password" type="password" class="form__input @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" oninput="validatePassword()">
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form__div">
                            <div class="form__icon">
                                <i class='bx bx-lock-alt'></i>
                            </div>
                            <div class="form__div-input">
                                <label for="password_confirmation" class="form__label">Confirm Password</label>
                                <input id="password_confirmation" type="password" class="form__input @error('password_confirmation') is-invalid @enderror" name="password_confirmation" required autocomplete="new-password">
                                @error('password_confirmation')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div id="password-rules" style="margin-top: 1rem; font-size: 0.875em; display: flex; flex-wrap: wrap; gap: 1rem;">
                        <div style="display: flex; justify-content: space-between; gap: 1rem;">
                            <p id="rule-length" style="color: red; margin: 0; flex: 1;">• At least 5 characters long</p>
                            <p id="rule-uppercase" style="color: red; margin: 0; flex: 1;">• At least one uppercase letter</p>
                        </div>
                        <div style="display: flex; justify-content: space-between; gap: 1rem; margin-top: 0.5rem;">
                            <p id="rule-lowercase" style="color: red; margin: 0; flex: 1;">• At least one lowercase letter</p>
                            <p id="rule-special" style="color: red; margin: 0; flex: 1;">• At least one special character (@#$%&)</p>
                        </div>
                    </div>

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

                    <button type="submit" class="form__button">
                        {{ __('Register') }}
                    </button>

                    <p class="form__text" style="text-align: center;">
                        <a href="{{ route('login') }}" class="form__button form__button--signup" style="display: inline-block; margin-top: 0.5rem;">
                            {{ __('Login') }}
                        </a>
                    </p>
                </form>
            </div>
        </div>
        
        <!-- jQuery and jQuery UI JS -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

        <!-- Flatpickr JS -->
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                flatpickr("#date_of_birth", {
                    dateFormat: "Y-m-d"
                });
            });
        </script>

        <!-- ===== MAIN JS ===== -->
        <script src="/js/main.js"></script>
    </body>
</html>
