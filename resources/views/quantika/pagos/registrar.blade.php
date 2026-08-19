<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Registrar pago | Quantika Pool</title>


    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {

            font-family: Arial, Helvetica, sans-serif;

            background:
                radial-gradient(
                    circle at top right,
                    rgba(38,198,218,.09),
                    transparent 35%
                ),
                #031d2b;

            color: white;

            min-height: 100vh;

        }

        .page {

            max-width: 950px;

            margin: auto;

            padding: 35px 25px;

        }

        .back {

            color: #7baabb;

            text-decoration: none;

            display: inline-block;

            margin-bottom: 25px;

        }

        .back:hover {
            color: #42d5ed;
        }

        .header {
            margin-bottom: 25px;
        }

        h1 {
            font-size: 32px;
            margin-bottom: 7px;
        }

        .subtitle {
            color: #729aaa;
            font-size: 14px;
        }

        .card {

            background:
                linear-gradient(
                    145deg,
                    rgba(8,61,81,.96),
                    rgba(3,38,53,.98)
                );

            border:
                1px solid
                rgba(55,191,218,.17);

            border-radius: 23px;

            padding: 27px;

        }

        .grid {

            display: grid;

            grid-template-columns:
                repeat(2,1fr);

            gap: 18px;

        }

        .field {

            display: flex;

            flex-direction: column;

            gap: 8px;

        }

        .full {
            grid-column: 1 / -1;
        }

        label {

            color: #8ab4c1;

            font-size: 11px;

            font-weight: 700;

            text-transform: uppercase;

            letter-spacing: .6px;

        }

        input,
        select,
        textarea {

            width: 100%;

            border: 1px solid
                rgba(83,190,212,.16);

            outline: none;

            background:
                rgba(1,29,42,.65);

            color: white;

            border-radius: 13px;

            padding:
                13px 14px;

            font-size: 13px;

        }

        input:focus,
        select:focus,
        textarea:focus {

            border-color: #35cde8;

            box-shadow:
                0 0 0 3px
                rgba(53,205,232,.07);

        }

        textarea {
            min-height: 100px;
            resize: vertical;
        }

        .amount {

            font-size: 22px;

            font-weight: 800;

        }

        .status-options {

            display: flex;

            gap: 10px;

            flex-wrap: wrap;

        }

        .status-option {

            flex: 1;

            min-width: 130px;

        }

        .status-option input {
            display: none;
        }

        .status-option span {

            display: flex;

            align-items: center;

            justify-content: center;

            min-height: 45px;

            border-radius: 13px;

            background:
                rgba(0,28,42,.5);

            border:
                1px solid
                rgba(255,255,255,.07);

            color: #789fac;

            font-size: 12px;

            font-weight: 700;

            cursor: pointer;

            transition: .2s;

        }

        .status-option input:checked + span {

            color: #42d5ed;

            border-color: #32cce7;

            background:
                rgba(40,202,228,.08);

        }

        .footer {

            display: flex;

            justify-content: flex-end;

            gap: 10px;

            margin-top: 25px;

        }

        .btn {

            border: none;

            text-decoration: none;

            display: inline-flex;

            align-items: center;

            justify-content: center;

            min-height: 45px;

            padding: 0 20px;

            border-radius: 13px;

            font-size: 13px;

            font-weight: 800;

            cursor: pointer;

        }

        .cancel {

            background:
                rgba(9,57,75,.65);

            color: #9bc0cb;

            border:
                1px solid
                rgba(75,191,216,.15);

        }

        .save {

            background:
                linear-gradient(
                    135deg,
                    #3dd8ed,
                    #20b9d2
                );

            color: #032536;

        }

        @media(max-width:650px) {

            .page {
                padding: 20px;
            }

            .grid {
                grid-template-columns: 1fr;
            }

            .full {
                grid-column: auto;
            }

            .footer {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }

        }

    </style>

</head>

<body>

<div class="page">

    <a
        href="{{ route('pagos.index') }}"
        class="back"
    >
        ← Volver a pagos
    </a>


    <div class="header">

        <h1>
            Registrar pago
        </h1>

        <p class="subtitle">
            Registra una mensualidad, inscripción
            o concepto adicional.
        </p>

    </div>


    <form>

        <div class="card">

            <div class="grid">


                <div class="field">

                    <label>
                        Alumno
                    </label>

                    <select>

                        <option>
                            Selecciona un alumno
                        </option>

                        <option>
                            María González
                        </option>

                        <option>
                            Carlos Ramírez
                        </option>

                        <option>
                            Valentina Sánchez
                        </option>

                    </select>

                </div>


                <div class="field">

                    <label>
                        Sucursal
                    </label>

                    <select>

                        <option>
                            QUANTIKA POOL · Sucursal 1
                        </option>

                        <option>
                            AQUALIX · Sucursal 2
                        </option>

                    </select>

                </div>


                <div class="field">

                    <label>
                        Concepto
                    </label>

                    <select>

                        <option>
                            Mensualidad
                        </option>

                        <option>
                            Inscripción
                        </option>

                        <option>
                            Concepto adicional
                        </option>

                    </select>

                </div>


                <div class="field">

                    <label>
                        Monto
                    </label>

                    <input
                        type="number"
                        placeholder="$0.00"
                        class="amount"
                    >

                </div>


                <div class="field">

                    <label>
                        Fecha de pago
                    </label>

                    <input
                        type="date"
                    >

                </div>


                <div class="field">

                    <label>
                        Método de pago
                    </label>

                    <select>

                        <option>
                            Efectivo
                        </option>

                        <option>
                            Transferencia
                        </option>

                        <option>
                            Tarjeta
                        </option>

                        <option>
                            Otro
                        </option>

                    </select>

                </div>


                <div class="field full">

                    <label>
                        Estado
                    </label>


                    <div class="status-options">


                        <label class="status-option">

                            <input
                                type="radio"
                                name="estado"
                                checked
                            >

                            <span>
                                🟡 Pendiente
                            </span>

                        </label>


                        <label class="status-option">

                            <input
                                type="radio"
                                name="estado"
                            >

                            <span>
                                🟢 Pagado
                            </span>

                        </label>


                        <label class="status-option">

                            <input
                                type="radio"
                                name="estado"
                            >

                            <span>
                                🔵 En revisión
                            </span>

                        </label>


                    </div>

                </div>


                <div class="field full">

                    <label>
                        Comprobante
                    </label>

                    <input
                        type="file"
                    >

                </div>


                <div class="field full">

                    <label>
                        Observaciones
                    </label>

                    <textarea
                        placeholder="Agrega alguna observación..."
                    ></textarea>

                </div>


            </div>


            <div class="footer">

                <a
                    href="{{ route('pagos.index') }}"
                    class="btn cancel"
                >
                    Cancelar
                </a>

                <button
                    type="button"
                    class="btn save"
                >
                    Registrar pago
                </button>

            </div>

        </div>

    </form>

</div>

</body>

</html>