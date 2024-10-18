<?php
	include '../../includes/db.php';
    $members = fetchLawFirmMembers($_SESSION['parent_id']);
?>

<div class="row">
    <div class="col-md-12">
        <table class="table table-borderless">
            <tr>
                <th>Firm Members</th>
                <?php if($_SESSION['user_role'] == 'superAdmin' || $_SESSION['user_role'] == 'General Administrator'):?>
                <td align="right">
                    <button type="button" class="btn btn-dark btn-sm" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                        <i class="bi bi-person-bounding-box"></i> Add New Member
                    </button>
                </td>
                <?php endif?>
            </tr>
        </table>
    </div>
    <?php foreach ($members as $member): ?>
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <img src="<?php echo get_gravatar($member['email'])?>" class="rounded-circle me-3" width="50" height="50" alt="<?php echo htmlspecialchars($member['names']); ?>">
                        <h5 class="card-title mb-0"><?php echo htmlspecialchars($member['title'] . ' ' . $member['names']); ?></h5>
                    </div>
                    <p class="card-text">
                        <strong>Email:</strong> <?php echo htmlspecialchars($member['email']); ?><br>
                        <strong>Phone Number:</strong> <?php echo htmlspecialchars($member['phonenumber']); ?><br>
                        <strong>Added Date:</strong> <?php echo date("D d M, Y", strtotime($member['joinDate'])); ?><br>
                        <strong>Designation:</strong> <?php echo htmlspecialchars($member['job']); ?><br>
                        <strong>Role:</strong> <?php echo htmlspecialchars($member['userRole']); ?><br>
                        <strong>Status:</strong> <?php echo $member['login_auth'] == 1 ? 'Active' : 'Suspended'; ?>
                    </p>
                    <div class="mb-3">
                        <strong>Permissions:</strong><br>
                        <?php
                        $permissions = [
                            'superAdmin' => 'Can use all functionalities',
                            'Legal Admin' => 'All Legal Matters, Not allowed on Finances',
                            'Finance Officer' => 'All Finance Matters, Not allowed on Legal Matters',
                            'General Administrator' => 'Can Add Users, Add Legal Matters, Not allowed on Finances'
                        ];
                        ?>
                        <p><?php echo htmlspecialchars($permissions[$member['userRole']] ?? 'No specific permissions'); ?></p>
                    </div>
                    <?php
					    if (!function_exists('renderButtons')) {
						    function renderButtons($conditions, $buttonHtml) {
						        if (array_sum($conditions) === count($conditions)) {
						            echo $buttonHtml;
						        }
						    }
						}
					?>
					
                    <div class="d-flex justify-content-end">
					    <?php
					    $currentUserRole = $_SESSION['user_role'];
					    $isSuperAdmin = ($currentUserRole === 'superAdmin');
					    $isGeneralAdmin = ($currentUserRole === 'General Administrator');
					    $memberIsSuperAdmin = ($member['userRole'] === 'superAdmin');

					    // Edit button
					    renderButtons(
					        [($isSuperAdmin || $isGeneralAdmin), (!$memberIsSuperAdmin || $isSuperAdmin)],
					        '<a href="#" class="btn btn-primary btn-sm editMemberData" data-id="' . $member['id'] . '">
					            <i class="bi bi-pen"></i> Edit
					        </a>'
					    );

					    // Delete button
					    renderButtons(
					        [$isSuperAdmin, (!$memberIsSuperAdmin || $isSuperAdmin)],
					        '<button class="btn btn-danger btn-sm deleteMember ms-2" data-id="' . $member['id'] . '" 
					            data-phone="' . $member['phonenumber'] . '" data-email="' . $member['email'] . '" 
					            data-names="' . $member['names'] . '">
					            <i class="bi bi-trash2"></i> Remove
					        </button>'
					    );

					    // Suspend/Grant Access button
					    renderButtons(
					        [$isSuperAdmin, (!$memberIsSuperAdmin || $isSuperAdmin)],
					        '<button class="btn ' . ($member['login_auth'] == 1 ? 'btn-warning' : 'btn-success') . ' btn-sm toggleAccess ms-2" 
					            data-id="' . $member['id'] . '" 
					            data-name="' . htmlspecialchars($member['names']) . '"
					            data-status="' . $member['login_auth'] . '">
					            <i class="bi ' . ($member['login_auth'] == 1 ? 'bi-lock' : 'bi-unlock') . '"></i> 
					            ' . ($member['login_auth'] == 1 ? 'Suspend' : 'Grant Access') . '
					        </button>'
					    );
					    ?>
					</div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

