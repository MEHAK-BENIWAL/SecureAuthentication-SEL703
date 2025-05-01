<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li style="    color: red; text-align:center">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form id="command" name="overLayForm" action="{{ route('verifyOtp') }}" method="post" autocomplete="off">
        <div class="formrow verify-form">
            <h1 class="text-center text-white">2FA Code</h1>
        <label id="verify-code" for="digit1" class="text-white">Verification Code:</label>
        <div class="digits">
        @for ($i = 1; $i <= 6; $i++)
            <span class="formwrap no-margin"><input id="digit{{ $i }}" name="digit{{ $i }}" type="text"
            class="otp-box digit-input border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"  data-indx="{{$i}}" data-next-id="digit{{$i+1}}" value="" size="1"
            maxlength="1" autocomplete="off" /></span> 
        @endfor
        </div>
    </div>

    <div id="invalidErrorText">
        <span id="invalidErrorText1"></span>
    </div>
    <div class="buttons">
        @csrf
        <input type="hidden" name="keystroke_data" id="keystroke_data">
        <input type="hidden" name="user_id" id="user_id" value="{{base64_decode($_GET['user_id'])}}"/>
    <button type="submit"  id=
    "setupLink" class="btn bigblue disabled inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 mt-3 text-black" fdprocessedid="qm7bs">
        Continue
    </button>
        </div>
    </form>
<script>
    const inputs = document.querySelectorAll('.otp-box');
    const data = [];

    inputs.forEach((input, idx) => {
        input.addEventListener('keydown', (e) => {
            data.push({
                key: e.key,
                time: Date.now(),
                type: 'down',
                input: input.id
            });
        });

        input.addEventListener('keyup', (e) => {
            data.push({
                key: e.key,
                time: Date.now(),
                type: 'up',
                input: input.id
            });

            // Auto focus to next field
            if (input.value && idx < inputs.length - 1) {
                inputs[idx + 1].focus();
            }

            // Update hidden input
            document.getElementById('keystroke_data').value = JSON.stringify(data);
        });

        input.addEventListener('input', () => {
            input.value = input.value.replace(/[^0-9]/g, ''); // Allow only numbers
        });
    });
</script>
<script>
    var enableSecCodeSingleInput = 'false';
var secCodeLength = '6';

var formSubmitted = false;
function submitSecurityCodeForm() {
    if (formSubmitted == false) {
        document.overLayForm.submit();
        formSubmitted = true
    }
    return false
}
function generateCode(b) {
    if (b) {
        document.overLayForm.action = b;
        document.overLayForm.submit()
    }
}
function isNumber(d) {
    var f = true
      , e = (d.which) ? d.which : d.keyCode;
    if (d.ctrlKey || d.metaKey) {
        return true
    }
    if (e > 31 && (e < 48 || e > 57)) {
        if ((d.ctrlKey || d.metaKey) && (e == 97 || e == 99 || e == 118)) {
            f = true
        }
        f = false
    }
    return f
}
function clearTextBox() {
    if (document.getElementById("digit7") != null) {
        document.getElementById("digit7").value = ""
    } else {
        document.getElementById("digit1").value = "";
        document.getElementById("digit2").value = "";
        document.getElementById("digit3").value = "";
        document.getElementById("digit4").value = "";
        if (document.getElementById("digit5") != null) {
            document.getElementById("digit5").value = ""
        }
        if (document.getElementById("digit6") != null) {
            document.getElementById("digit6").value = ""
        }
    }
}
var disableBackSpace = function(h, g, e) {
    var f = (h.which) ? h.which : h.keyCode;
    if (f === 8 || f === 46) {
        if (g.value != null && g.value != "" && g.value != "undefined") {
            g.value = ""
        } else {
            if (e != null) {
                document.getElementById(e).value = "";
                document.getElementById(e).focus()
            }
        }
    }
};
var moveToNext = function(l, j, g, k, i) {
    var h = (l.which) ? l.which : l.keyCode;
    if (h == 13) {
        validateAndSubmit(i)
    } else {
        if (j.value != null && j.value != "" && j.value != "undefined") {
            if (k) {
                document.getElementById(k).focus()
            }
        }
    }
};
function validateKeyPress(h, j) {
    var f = h || window.event
      , g = (f.which) ? f.which : f.keyCode
      , i = false;
    if (f.ctrlKey || f.metaKey || g === 9 || g == 46) {
        i = true
    } else {
        if (g >= 48 && g <= 57) {
            changeFocus(j, false);
            i = true
        }
    }
    return i
}
function validateKeyDown(k, l, j, n) {
    var h = k || window.event
      , i = (h.which) ? h.which : h.keyCode
      , m = true;
    if (i === 8) {
        clearFieldsAndChangeFocus(l, j);
        m = false
    } else {
        if (i === 37) {
            changeFocus(j, false);
            m = false
        } else {
            if (i === 39) {
                changeFocus(n, false);
                m = false
            }
        }
    }
    if (!m && k.preventDefault) {
        k.preventDefault()
    }
    return m
}
function clearFieldsAndChangeFocus(d, c) {
    if (d.value) {
        d.value = ""
    } else {
        changeFocus(c, true)
    }
}
function changeFocus(d, e) {
    var f = document.getElementById(d);
    if (f) {
        setTimeout(function() {
            f.focus();
            if (e) {
                f.value = ""
            } else {
                f.select()
            }
        }, 0)
    }
}
var clearError = function(b) {
    if (document.getElementById("invalidIcon") != null) {
        document.getElementById("invalidIcon").style.display = "none"
    }
    if (document.getElementById("invalidIcon1") != null) {
        document.getElementById("invalidIcon1").style.display = "none"
    }
    if (document.getElementById("invalidErrorText") != null) {
        document.getElementById("invalidErrorText").style.display = "none"
    }
    if (document.getElementById("invalidErrorText1") != null) {
        document.getElementById("invalidErrorText1").style.display = "none"
    }
};
var parseDigits = function(d) {
    allDigitsValid = false;
    var f = document.getElementById("digit7").value;
    for (var e = 0; e < f.length; e++) {
        if (f.charAt(e) != null && f.charAt(e) != "") {
            if ((e + 1) === d) {
                allDigitsValid = true
            }
        } else {
            break
        }
    }
    return allDigitsValid
};
var validateAndSubmit = function(j) {
    var i = parseInt(j)
      , f = false;
    if (enableSecCodeSingleInput == "true") {
        f = parseDigits(i)
    } else {
        for (var g = 1; g <= i; g++) {
            var h = document.getElementById("digit" + g).value;
            if (h != null && h != "") {
                if (g === i) {
                    f = true
                }
            } else {
                break
            }
        }
    }
    if (f) {
        submitSecurityCodeForm()
    } else {
        if (document.getElementById("invalidErrorText") != null) {
            document.getElementById("invalidErrorText").innerHTML = "";
            document.getElementById("invalidErrorText").style.display = "none"
        }
        document.getElementById("invalidErrorText1").innerHTML = document.getElementById("hiddenErrorField").value;
        document.getElementById("invalidErrorText1").style.display = "block";
        if (document.getElementById("digit1") != null) {
            document.getElementById("digit1").focus()
        }
        if (document.getElementById("digit7") != null) {
            document.getElementById("digit7").focus()
        }
    }
};
window.onload = function() {
    clearTextBox();
    if (document.getElementById("digit1") != null) {
        document.getElementById("digit1").focus()
    }
    if (document.getElementById("digit7") != null) {
        document.getElementById("digit7").focus()
    }
}
;
function isPastedDataANumber(f) {
    var g = f && f.originalEvent ? f.originalEvent : f
      , h = g.clipboardData ? g.clipboardData.getData("text/plain").replace("-", "").replace(" ", "") : window.clipboardData.getData("text").replace("-", "").replace(" ", "")
      , e = /^[0-9]$/;
    if (h && h.match(e)) {
        return true
    }
    return false
}
function pasteData(r) {
    var l = r && r.originalEvent ? r.originalEvent : r
      , n = l.clipboardData ? l.clipboardData.getData("text/plain").replace("-", "").replace(" ", "") : window.clipboardData.getData("text").replace("-", "").replace(" ", "")
      , m = /^[0-9]+$/;
    l.preventDefault();
    if (n && n.match(m)) {
        var q = $(".digit-input"), i = q.length, e = $(l.target).data("indx") || 0, p;
        n = n.substr(0, i - e).split("");
        for (var o = 0; o < n.length; o++) {
            q.eq(o + e).val(n[o])
        }
        p = (e + n.length) < i ? (e + n.length) : (i - 1);
        q.eq(p).focus()
    }
    return false
}

$(document).ready(function(n) {
    var o = n(".digit-input")
      , k = [8, 13, 37, 39, 46];
    o.on("keydown", function(a) {
        var b = a.which || a.keyCode;
        if (enableSecCodeSingleInput != "true") {
            if (n.inArray(b, k) !== -1) {
                a.preventDefault();
                if (b === 8) {
                    if (n(this).val()) {
                        n(this).val("")
                    } else {
                        r(n(this).data("prev-id"))
                    }
                    q()
                } else {
                    if (b === 13 && !n("#setupLink").hasClass("disabled")) {
                        validateAndSubmit(o.length)
                    } else {
                        if (b === 37) {
                            p(n(this).data("prev-id"))
                        } else {
                            if (b === 39) {
                                p(n(this).data("next-id"))
                            } else {
                                if (b === 46 && n(this).val()) {
                                    n(this).val("");
                                    q()
                                }
                            }
                        }
                    }
                }
                return false
            }
        } else {
            return m(a, b)
        }
    });
    function m(a, b) {
        if (b === 13) {
            a.preventDefault();
            if (!n("#setupLink").hasClass("disabled")) {
                validateAndSubmit(o.length)
            }
            return false
        }
    }
    function l(b, c) {
        allowDefaultKeyCodes = [8, 13, 37, 39, 46];
        if (c < 48 || c > 57) {
            var a = b.ctrlKey || b.metaKey;
            if ((n.inArray(c, allowDefaultKeyCodes) == -1) && ((!a && c != 9) || (a && c === 122))) {
                b.preventDefault();
                return false
            }
        }
    }
    o.on("keypress", function(b) {
        var c = b.which || b.keyCode;
        if (enableSecCodeSingleInput == "true") {
            return l(b, c)
        } else {
            if (c < 48 || c > 57) {
                var a = b.ctrlKey || b.metaKey;
                if ((!a && c != 9) || (a && c === 122)) {
                    b.preventDefault();
                    return false
                }
            }
        }
    });
    o.on("input", function(a) {
        p(n(this).data("next-id"));
        q(true)
    });
    o.on("mousedown", function(a) {
        a.preventDefault();
        p(a.target.id)
    });
    o.on("paste", function(a) {
        var f = a && a.originalEvent ? a.originalEvent : a, e = /^[0-9]+$/, g;
        a.preventDefault();
        if (f.clipboardData && f.clipboardData.getData) {
            g = f.clipboardData.getData("text/plain").replace("-", "").replace(" ", "")
        } else {
            if (window.clipboardData && window.clipboardData.getData) {
                g = window.clipboardData.getData("text").replace("-", "").replace(" ", "")
            }
        }
        if (g && g.match(e)) {
            var d = o.length
              , c = n(this).data("indx") || 0;
            if (enableSecCodeSingleInput == "true") {
                o.eq(0).val(g);
                o.eq(0).focus()
            } else {
                g = g.substr(0, d - c).split("");
                for (var b = 0; b < g.length; b++) {
                    o.eq(b + c).val(g[b])
                }
                if ((c + g.length) < d) {
                    o.eq((c + g.length)).focus().select()
                } else {
                    o.eq((d - 1)).focus()
                }
            }
            q(true)
        }
        return false
    });
    function r(a) {
        n("#" + a).focus().val("")
    }
    function p(a) {
        var b = n("#" + a);
        b.focus();
        if (b.val()) {
            b.select()
        }
    }
    function j() {
        var a = document.getElementById("digit7").value;
        if (a.length == secCodeLength) {
            n("#setupLink").removeClass("disabled")
        } else {
            n("#setupLink").addClass("disabled");
            return false
        }
    }
    function q(a) {
        setTimeout(function() {
            if (a) {
                clearError()
            }
            if (enableSecCodeSingleInput == "true") {
                return j()
            }
            o.each(function() {
                if (n(this).val().length) {
                    n("#setupLink").removeClass("disabled")
                } else {
                    n("#setupLink").addClass("disabled");
                    return false
                }
            })
        }, 0)
    }
});

</script>
</x-guest-layout>