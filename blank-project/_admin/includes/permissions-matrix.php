<?php
if ($tableSegment == 'groups') {
    if ($mode == 'update') {
        $sqlPreviousGroups = "SELECT " . suJsonExtract('data', 'add_access') . "," . suJsonExtract('data', 'preview_access') . "," . suJsonExtract('data', 'sort_access') . "," . suJsonExtract('data', 'view_access') . "," . suJsonExtract('data', 'update_access') . "," . suJsonExtract('data', 'delete_access') . "," . suJsonExtract('data', 'duplicate_access') . "," . suJsonExtract('data', 'download_csv_access') . "," . suJsonExtract('data', 'download_pdf_access') . " FROM groups WHERE id ='" . $rid . "' AND live='Yes' LIMIT 0,1";
        $resultPreviousGroups = suQuery($sqlPreviousGroups);
        $resultPreviousGroups = $resultPreviousGroups['result'][0];
        $resultPreviousGroups['add_access'] = json_decode($resultPreviousGroups['add_access']);
        $resultPreviousGroups['preview_access'] = json_decode($resultPreviousGroups['preview_access']);
        $resultPreviousGroups['sort_access'] = json_decode($resultPreviousGroups['sort_access']);
        $resultPreviousGroups['view_access'] = json_decode($resultPreviousGroups['view_access']);
        $resultPreviousGroups['update_access'] = json_decode($resultPreviousGroups['update_access']);
        $resultPreviousGroups['delete_access'] = json_decode($resultPreviousGroups['delete_access']);
        $resultPreviousGroups['duplicate_access'] = json_decode($resultPreviousGroups['duplicate_access']);
        $resultPreviousGroups['download_csv_access'] = json_decode($resultPreviousGroups['download_csv_access']);
        $resultPreviousGroups['download_pdf_access'] = json_decode($resultPreviousGroups['download_pdf_access']);
    }
    ?>
    <div class="clearfix"></div>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="form-group">
            <?php
            //Groups
            $sqlGr = "SELECT id,title,slug,show_sorting_module  FROM " . STRUCTURE_TABLE_NAME . " WHERE /*display='Yes' AND*/ live='Yes' ORDER BY title";
            $resultGr = suQuery($sqlGr);
            $resultGr['result'] = suUnstrip($resultGr['result']);
            $resultGr = $resultGr['result'];
            //print_array($resultGr);
            $sr = 1;
            ?>
            <p><a href="javascript:;" onclick="doCheckUncheck(1);"><i class="fa fa-check-square-o"></i> Check All</a> / <a href="javascript:;" onclick="doCheckUncheck(0);"><i class="fa fa-square-o"></i> Uncheck All</a></p>
            <table class="table table-striped table-hover tablex gallery clearfix">

                <?php
                foreach ($resultGr as $keyGr => $valueGr) {
                    ?>
                    <tr>
                        <td><span class="badge"><?php echo $sr; ?></span></td>
                        <td>
                            <?php echo $valueGr['title']; ?>



                        </td>
                        <td>

                            <a href="javascript:;" onclick="doCheckUncheckValued(1, '<?php echo $valueGr['slug']; ?>');"><i class="fa fa-check-square-o"></i></a> / <a href="javascript:;" onclick="doCheckUncheckValued(0, '<?php echo $valueGr['slug']; ?>');"><i class="fa fa-square-o"></i></a>



                        </td>
                        <td>
                            <div class="pretty p-default p-curve p-thick size-80">
                                <?php
                                if (in_array($valueGr['slug'], $resultPreviousGroups['add_access'])) {
                                    $checkedAA = "checked='checked'";
                                } else {
                                    $checkedAA = "";
                                }
                                ?>
                                <input title='Add' <?php echo $checkedAA; ?> type="checkbox" name="add_access[]" id="add_access[]" value="<?php echo $valueGr['slug']; ?>"  />
                                <div class="state p-warning">
                                    <label>Add</label>
                                </div>
                            </div>

                            <div class="pretty p-default p-curve p-thick size-80">
                                <?php
                                if (in_array($valueGr['slug'], $resultPreviousGroups['view_access'])) {
                                    $checkedVA = "checked='checked'";
                                } else {
                                    $checkedVA = "";
                                }
                                ?>
                                <input title='View' <?php echo $checkedVA; ?> type="checkbox" name="view_access[]" id="view_access[]" value="<?php echo $valueGr['slug']; ?>"  />
                                <div class="state p-warning">
                                    <label>View</label>
                                </div>
                            </div>

                            <div class="pretty p-default p-curve p-thick size-80">
                                <?php
                                if (in_array($valueGr['slug'], $resultPreviousGroups['preview_access'])) {
                                    $checkedPA = "checked='checked'";
                                } else {
                                    $checkedPA = "";
                                }
                                ?>
                                <input title='Preview' <?php echo $checkedPA; ?> type="checkbox" name="preview_access[]" id="preview_access[]" value="<?php echo $valueGr['slug']; ?>"  />
                                <div class="state p-warning">
                                    <label>Preview</label>
                                </div>
                            </div>


                            <div class="pretty p-default p-curve p-thick size-80">
                                <?php
                                if (in_array($valueGr['slug'], $resultPreviousGroups['update_access'])) {
                                    $checkedUA = "checked='checked'";
                                } else {
                                    $checkedUA = "";
                                }
                                ?>
                                <input title='Update' <?php echo $checkedUA; ?> type="checkbox" name="update_access[]" id="update_access[]" value="<?php echo $valueGr['slug']; ?>"  />
                                <div class="state p-warning">
                                    <label>Update</label>
                                </div>
                            </div>

                            <div class="pretty p-default p-curve p-thick size-80">
                                <?php
                                if (in_array($valueGr['slug'], $resultPreviousGroups['delete_access'])) {
                                    $checkedDA = "checked='checked'";
                                } else {
                                    $checkedDA = "";
                                }
                                ?>
                                <input title='Delete' <?php echo $checkedDA; ?> type="checkbox" name="delete_access[]" id="delete_access[]" value="<?php echo $valueGr['slug']; ?>"  />
                                <div class="state p-warning">
                                    <label>Delete</label>
                                </div>
                            </div>

                            <div class="pretty p-default p-curve p-thick size-80">
                                <?php
                                if (in_array($valueGr['slug'], $resultPreviousGroups['duplicate_access'])) {
                                    $checkedDUA = "checked='checked'";
                                } else {
                                    $checkedDUA = "";
                                }
                                ?>
                                <input title='Duplicate' <?php echo $checkedDUA; ?> type="checkbox" name="duplicate_access[]" id="duplicate_access[]" value="<?php echo $valueGr['slug']; ?>"  />
                                <div class="state p-warning">
                                    <label>Duplicate</label>
                                </div>
                            </div>
                            <div class="pretty p-default p-curve p-thick size-80">
                                <?php
                                if (in_array($valueGr['slug'], $resultPreviousGroups['download_csv_access'])) {
                                    $checkedDCA = "checked='checked'";
                                } else {
                                    $checkedDCA = "";
                                }
                                ?>
                                <input title='Download CSV' <?php echo $checkedDCA; ?> type="checkbox" name="download_csv_access[]" id="download_csv_access[]" value="<?php echo $valueGr['slug']; ?>"  />
                                <div class="state p-warning">
                                    <label>Download CSV</label>
                                </div>
                            </div>

                            <div class="pretty p-default p-curve p-thick size-80">
                                <?php
                                if (in_array($valueGr['slug'], $resultPreviousGroups['download_pdf_access'])) {
                                    $checkedDPA = "checked='checked'";
                                } else {
                                    $checkedDPA = "";
                                }
                                ?>
                                <input title='Download PDF' <?php echo $checkedDPA; ?> type="checkbox" name="download_pdf_access[]" id="download_pdf_access[]" value="<?php echo $valueGr['slug']; ?>"  />
                                <div class="state p-warning">
                                    <label>Download PDF</label>
                                </div>
                            </div>
                            <!-- Sort Access -->
                            <?php if ($valueGr['show_sorting_module'] == 'Yes') { ?>
                                <div class="pretty p-default p-curve p-thick size-80">
                                    <?php
                                    if (in_array($valueGr['slug'], $resultPreviousGroups['sort_access'])) {
                                        $checkedSA = "checked='checked'";
                                    } else {
                                        $checkedSA = "";
                                    }
                                    ?>
                                    <input title='Sort' <?php echo $checkedSA; ?> type="checkbox" name="sort_access[]" id="sort_access[]" value="<?php echo $valueGr['slug']; ?>"  />
                                    <div class="state p-warning">
                                        <label>Sort</label>
                                    </div>
                                </div>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php
                    $sr++;
                }
                ?>
            </table>

        </div>
    </div>
<?php } ?>