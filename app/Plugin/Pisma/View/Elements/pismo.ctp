<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="bodyTable">
    <tr>
        <td align="center" valign="top">
            <table border="0" cellpadding="10" cellspacing="0" width="700" id="emailContainer">
                <tr>
                    <td align="left" valign="top">
                        <table border="0" cellpadding="10" cellspacing="0" width="100%" id="docAdress">
                            <tr>
                                <td align="left" valign="top">
                                    <?= '<p>' . str_replace("\n", '</p><p>', $pismo['nadawca']) . '</p>' ?>
                                </td>
                                <td align="right" valign="top">
                                    <?= '<p>' . $this->Czas->dataSlownie( $pismo['data_pisma'] ) . '</p>' ?>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="right" valign="top">
                        <table border="0" cellpadding="10" cellspacing="0" width="100%" id="docAdress">
                            <tr>
                                <td align="right" valign="top">
                                    <?= $pismo['to_str'] ?>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="center" valign="top">
                        <table border="0" cellpadding="5" cellspacing="0" width="100%" id="docTitle">
                            <tr>
                                <td align="center" valign="top">
                                    <b><?= $pismo['tytul'] ?></b>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="left" valign="top">
                        <table border="0" cellpadding="10" cellspacing="0" width="100%" id="docContent">
                            <tr>
                                <td align="left" valign="top">
                                    <?= $pismo['content'] ?>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="center" valign="top">
                        <table border="0" cellpadding="10" cellspacing="0" width="100%" id="pismoSignature">
                            <tr>
                                <td align="right" valign="top">
                                    <?=  $pismo['from_signature'] ?>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>