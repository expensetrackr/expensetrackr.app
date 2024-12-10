import { router } from "@inertiajs/react";
import { parseAsStringEnum, useQueryState } from "nuqs";
import { useState } from "react";
import MailCloseIcon from "virtual:icons/ri/mail-close-line";
import { route } from "ziggy-js";

import { ActionSection } from "#/components/action-section.tsx";
import { Button } from "#/components/button.tsx";
import {
    Dialog,
    DialogActions,
    DialogDescription,
    DialogHeader,
    DialogIcon,
    DialogTitle,
} from "#/components/dialog.tsx";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "#/components/table.tsx";
import { type User, type Workspace, type WorkspaceInvitation, type WorkspacePermissions } from "#/types/index.ts";
import { Action, getAction } from "#/utils/action.ts";

interface UserMembership extends User {
    membership: {
        role: string;
    };
}

interface WorkspaceMemberInvitationsProps {
    workspace: Workspace & {
        owner: User;
        workspace_invitations: WorkspaceInvitation[];
        members: UserMembership[];
    };
    permissions: WorkspacePermissions;
}

export function WorkspaceMemberInvitations({ workspace, permissions }: WorkspaceMemberInvitationsProps) {
    return (
        <ActionSection
            description="These people have been invited to your workspace and have been sent an invitation email. They may join the workspace by accepting the email invitation."
            title="Pending workspace invitations"
        >
            <Table bleed>
                <TableHead>
                    <TableRow>
                        <TableHeader>Email address</TableHeader>
                        {permissions.canRemoveWorkspaceMembers ? (
                            <TableHeader className="relative w-0">
                                <span className="sr-only">Actions</span>
                            </TableHeader>
                        ) : null}
                    </TableRow>
                </TableHead>
                <TableBody>
                    {workspace.workspace_invitations.length > 0 ? (
                        workspace.workspace_invitations?.map((invitation) => (
                            <TableRow key={invitation.id}>
                                <TableCell>{invitation.email}</TableCell>
                                {permissions.canRemoveWorkspaceMembers ? (
                                    <TableCell>
                                        <CancelInvitation invitation={invitation} />
                                    </TableCell>
                                ) : null}
                            </TableRow>
                        ))
                    ) : (
                        <TableRow>
                            <TableCell colSpan={3}>
                                <div className="flex justify-center py-12">
                                    <p className="text-paragraph-sm text-(--text-sub-600)">
                                        There are no pending invitations.
                                    </p>
                                </div>
                            </TableCell>
                        </TableRow>
                    )}
                </TableBody>
            </Table>
        </ActionSection>
    );
}

function CancelInvitation({ invitation }: { invitation: WorkspaceInvitation }) {
    const [action, setAction] = useQueryState("action", parseAsStringEnum<Action>(Object.values(Action)));
    const [isCancelling, setCancelling] = useState(false);

    function cancelWorkspaceInvitation(invitation: WorkspaceInvitation) {
        setCancelling(true);

        router.delete(route("workspace-invitations.destroy", [invitation.id]), {
            preserveScroll: true,
            onSuccess: async () => {
                await setAction(null);
            },
        });

        setCancelling(false);
    }

    return (
        <>
            <Button
                $color="error"
                $size="sm"
                $variant="stroke"
                onClick={() => setAction(getAction("WorkspaceInvitationsDestroy", invitation.id))}
            >
                Cancel invitation
            </Button>

            <Dialog
                onClose={() => setAction(null)}
                open={action === getAction("WorkspaceInvitationsDestroy", invitation.id)}
            >
                <DialogHeader>
                    <DialogIcon>
                        <MailCloseIcon className="size-6 text-(--icon-sub-600)" />
                    </DialogIcon>

                    <div className="flex flex-1 flex-col gap-1">
                        <DialogTitle>Cancel invitation</DialogTitle>
                        <DialogDescription>Are you sure you want to cancel this invitation?</DialogDescription>
                    </div>
                </DialogHeader>

                <DialogActions>
                    <Button
                        $color="neutral"
                        $size="sm"
                        $variant="stroke"
                        className="w-full"
                        disabled={isCancelling}
                        onClick={() => setAction(null)}
                    >
                        Cancel
                    </Button>
                    <Button
                        $color="error"
                        $size="sm"
                        className="w-full"
                        disabled={isCancelling}
                        onClick={() => cancelWorkspaceInvitation(invitation)}
                    >
                        {isCancelling ? "Cancelling..." : "Yes, cancel it"}
                    </Button>
                </DialogActions>
            </Dialog>
        </>
    );
}
