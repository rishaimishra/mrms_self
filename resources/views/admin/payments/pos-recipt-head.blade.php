<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Document</title>

<style>
    body {
        font-family: sans-serif;
    }

    * {
        margin: 0;
        padding: 0;
    }

    .wrapper {
        width: 840px;
        height: 1188px;
        padding: 20px 20px 20px;
        position: relative;
        /* border: 2px solid red; */
    }

    .wrapper:before {
        display: block;
        background: url('{{ asset('images/logo23.png') }}');
        background-size: 70%;
        width: 100%;
        height: 100%;
        position: absolute;
        margin-top: 20%;
        top: 0;
        left: 0;
        background-repeat: no-repeat;
        background-position: 50% 48%;
        z-index: -1;
        opacity: 20%;
    }

    /* .watermark {
            position: absolute;
            top: 20%;
            left: 50%;
            transform: translate(-50%, 0);
            width: 70%;
            opacity: 0.2;
            z-index: -1;
        } */

    .d-flex {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        align-items: center;
        padding: 0 0 20px;
        margin: 0 0 40px;
        border-bottom: 1px solid #c4c4c4;
    }

    .col-3 {
        width: 15%;
    }

    .col-6 {
        width: 70%;
        text-align: center;
    }

    .h4 {
        font-size: 22px;
        font-weight: 900;
        margin: 0 0 10px;
    }

    .h5 {
        font-size: 16px;
        font-weight: 700;
        color: #777;
        margin: 0 0 6px;
    }

    .h6 {
        font-size: 16px;
        font-weight: 700;
        color: #777;
    }

    .t-logo {
        max-width: 100%;
        height: auto;
        /* width: 140px; */
    }

    table {
        font-size: 17px;
        font-family: serif;
        width: 90%;
        margin: auto;
    }

    td {
        padding: 10px 10px 7px;
        font-weight: 600;
        color: #626262;
    }

    .blue-bg {
        background: #116cca;
        color: #fff;
        padding: 7px 10px 7px;
    }

    table tr td:first-child {
        width: 220px;
    }

    .text-grey {
        color: grey;
        padding: 0 0 0 20px;
    }

    .bb-date {
        margin: 0 0 0 150px;
    }


    @media only screen and (max-width:600px) {
        .wrapper {
            width: auto;
            height: auto
        }

        .d-flex {}

        .h4 {
            font-size: 12px
        }

        .h5 {
            font-size: 10px
        }

        .h6 {
            font-size: 10px
        }

        .bb-date {
            margin: 0
        }

        td {
            font-size: 8px;
            padding: 5px 4px 5px
        }

        table tr td:first-child {
            width: 100px
        }

        table {
            width: 100%;
            margin-top: -20px;
        }
    }

    /* @media only screen and (max-width:600px){table tr td:first-child{width:150px}} */
    tbody tr:first-child td:nth-child(2) {
        text-align: left;
    }

    tbody tr:first-child td:nth-child(4),
    tbody tr td:nth-child(2) {
        text-align: right;
    }

    @media only screen and (max-width:600px) {

        table tr:first-child td:first-child {
            white-space: nowrap;
        }

        table tr td:first-child {
            width: 100px;
            white-space: pre;
        }

    }
</style>
