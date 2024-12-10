import { useForm } from "@inertiajs/react";
import { parseAsStringEnum, useQueryState } from "nuqs";
import EraserIcon from "virtual:icons/ri/eraser-line";
import FolderShield2Icon from "virtual:icons/ri/folder-shield-2-line";
import LogoutCircleRIcon from "virtual:icons/ri/logout-circle-r-line";
import More2Icon from "virtual:icons/ri/more-2-line";
import ShieldUserIcon from "virtual:icons/ri/shield-user-line";
import UserMinusIcon from "virtual:icons/ri/user-minus-line";
import { route } from "ziggy-js";

import { ActionSection } from "#/components/action-section.tsx";
import { Avatar } from "#/components/avatar.tsx";
import { Button } from "#/components/button.tsx";
import {
    Dialog,
    DialogActions,
    DialogBody,
    DialogDescription,
    DialogHeader,
    DialogIcon,
    DialogTitle,
} from "#/components/dialog.tsx";
import { Dropdown, DropdownButton, DropdownItem, DropdownLabel, DropdownMenu } from "#/components/dropdown.tsx";
import { Field, Hint, Label } from "#/components/form/fieldset.tsx";
import { Select } from "#/components/form/select.tsx";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "#/components/table.tsx";
import { useUser } from "#/hooks/use-user.ts";
import {
    type Role,
    type User,
    type Workspace,
    type WorkspaceInvitation,
    type WorkspacePermissions,
} from "#/types/index.ts";
import { Action, getAction } from "#/utils/action.ts";
import { AddWorkspaceMemberForm } from "./add-workspace-member-form.tsx";

interface UserMembership extends User {
    membership: {
        role: string;
    };
}

interface WorkspaceMemberManagerProps {
    workspace: Workspace & {
        owner: User;
        workspace_invitations: WorkspaceInvitation[];
        members: UserMembership[];
    };
    availableRoles: Role[];
    permissions: WorkspacePermissions;
}

export function WorkspaceMemberManager({ workspace, availableRoles, permissions }: WorkspaceMemberManagerProps) {
    const [, setAction] = useQueryState("action", parseAsStringEnum<Action>(Object.values(Action)));
    const currentUser = useUser();

    function displayableRole(role: string) {
        return availableRoles.find((r) => r.key === role)?.name;
    }

    return (
        <ActionSection
            action={
                permissions.canAddWorkspaceMembers ? (
                    <AddWorkspaceMemberForm availableRoles={availableRoles} workspace={workspace} />
                ) : null
            }
            description="All of the people that are part of this workspace."
            title="Members"
        >
            <Table bleed>
                <TableHead>
                    <TableRow>
                        <TableHeader>Member full name</TableHeader>
                        <TableHeader>Email address</TableHeader>
                        <TableHeader>Role</TableHeader>
                        <TableHeader className="relative w-0">
                            <span className="sr-only">Actions</span>
                        </TableHeader>
                    </TableRow>
                </TableHead>
                <TableBody>
                    {workspace.members.length > 0 ? (
                        workspace.members?.map((user) => (
                            <TableRow key={user.id}>
                                <TableCell>
                                    <div className="inline-flex items-center gap-3">
                                        <Avatar
                                            alt={user.name}
                                            className="size-8"
                                            src={user.profile_photo_url}
                                            user={user}
                                        />
                                        <p className="text-(--text-strong-950)">{user.name}</p>
                                    </div>
                                </TableCell>
                                <TableCell>{user.email}</TableCell>
                                <TableCell>{displayableRole(user.membership.role)}</TableCell>

                                {(permissions.canAddWorkspaceMembers && availableRoles.length > 0) ||
                                permissions.canRemoveWorkspaceMembers ||
                                currentUser.id === user.id ? (
                                    <TableCell>
                                        <div className="-mx-3 -my-1.5 sm:-mx-2.5">
                                            <Dropdown>
                                                <DropdownButton
                                                    $color="neutral"
                                                    $size="xs"
                                                    $variant="ghost"
                                                    aria-label="Actions"
                                                >
                                                    <More2Icon className="size-5" />
                                                </DropdownButton>
                                                <DropdownMenu anchor="bottom end">
                                                    {permissions.canAddWorkspaceMembers && availableRoles.length > 0 ? (
                                                        <DropdownItem
                                                            onClick={() =>
                                                                setAction(getAction("WorkspaceMembersUpdate", user.id))
                                                            }
                                                        >
                                                            <ShieldUserIcon />
                                                            <DropdownLabel>Update role</DropdownLabel>
                                                        </DropdownItem>
                                                    ) : null}

                                                    {permissions.canRemoveWorkspaceMembers ? (
                                                        <DropdownItem
                                                            className="text-state-error-base data-focus:bg-(--color-red-alpha-10) [&>[data-slot=icon]]:text-state-error-base"
                                                            onClick={() =>
                                                                setAction(getAction("WorkspaceMembersDestroy", user.id))
                                                            }
                                                        >
                                                            <UserMinusIcon />
                                                            <DropdownLabel>Remove member</DropdownLabel>
                                                        </DropdownItem>
                                                    ) : null}

                                                    {currentUser.id === user.id ? (
                                                        <DropdownItem
                                                            className="text-state-error-base data-focus:bg-(--color-red-alpha-10) [&>[data-slot=icon]]:text-state-error-base"
                                                            onClick={() =>
                                                                setAction(getAction("WorkspaceMembersDestroy", user.id))
                                                            }
                                                        >
                                                            <LogoutCircleRIcon />
                                                            <DropdownLabel>Leave workspace</DropdownLabel>
                                                        </DropdownItem>
                                                    ) : null}
                                                </DropdownMenu>
                                            </Dropdown>

                                            {permissions.canAddWorkspaceMembers && availableRoles.length > 0 ? (
                                                <ManageRoleDialog
                                                    availableRoles={availableRoles}
                                                    user={user}
                                                    workspace={workspace}
                                                />
                                            ) : null}

                                            {permissions.canRemoveWorkspaceMembers || currentUser.id === user.id ? (
                                                <RemoveMemberDialog
                                                    user={currentUser.id === user.id ? currentUser : user}
                                                    workspace={workspace}
                                                    {...(currentUser.id === user.id
                                                        ? {
                                                              dialogTitle: "Leave workspace",
                                                              dialogDescription:
                                                                  "Are you sure you would like to leave this workspace?",
                                                              dialogSubmitLabel: "Yes, leave it",
                                                          }
                                                        : {})}
                                                />
                                            ) : null}
                                        </div>
                                    </TableCell>
                                ) : null}
                            </TableRow>
                        ))
                    ) : (
                        <TableRow>
                            <TableCell colSpan={3}>
                                <div className="flex justify-center py-12">
                                    <p className="text-paragraph-sm text-(--text-sub-600)">
                                        There are no members in this workspace yet.
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

function ManageRoleDialog({
    workspace,
    user,
    availableRoles,
}: {
    workspace: Workspace;
    user: UserMembership;
    availableRoles: Role[];
}) {
    const [action, setAction] = useQueryState("action", parseAsStringEnum<Action>(Object.values(Action)));
    const form = useForm({
        role: user.membership.role,
    });

    function onSubmit(e: React.FormEvent) {
        e.preventDefault();

        form.put(route("workspace-members.update", [workspace.id, user.id]), {
            preserveScroll: true,
            onSuccess: async () => {
                form.reset();
                await setAction(null);
            },
        });
    }

    return (
        <Dialog
            onClose={() => setAction(null)}
            open={action === Action.WorkspaceMembersUpdate.replace("{id}", user.id.toString())}
        >
            <DialogHeader>
                <DialogIcon>
                    <FolderShield2Icon className="size-6 text-(--icon-sub-600)" />
                </DialogIcon>

                <div className="flex flex-1 flex-col gap-1">
                    <DialogTitle>Manage role</DialogTitle>
                    <DialogDescription>Select the new role for this workspace member.</DialogDescription>
                </div>
            </DialogHeader>

            <DialogBody>
                <form id={`update-workspace-members-role-${user.id}-form`} onSubmit={onSubmit}>
                    <Field>
                        <Label>Role</Label>
                        <Select
                            invalid={!!form.errors.role}
                            name="role"
                            onChange={(e) => form.setData("role", e.target.value)}
                            value={form.data.role}
                        >
                            {availableRoles.map((role) => (
                                <option key={role.key} value={role.key}>
                                    {role.name}
                                </option>
                            ))}
                        </Select>
                        {form.errors.role && <Hint invalid>{form.errors.role}</Hint>}
                    </Field>
                </form>
            </DialogBody>

            <DialogActions>
                <Button
                    $color="neutral"
                    $size="sm"
                    $variant="stroke"
                    className="w-full"
                    disabled={form.processing}
                    onClick={() => setAction(null)}
                >
                    Cancel
                </Button>
                <Button
                    $size="sm"
                    className="w-full"
                    disabled={form.processing}
                    form={`manage-role-form-${user.id}`}
                    type="submit"
                >
                    {form.processing ? "Updating..." : "Update role"}
                </Button>
            </DialogActions>
        </Dialog>
    );
}

function RemoveMemberDialog({
    workspace,
    user,
    dialogTitle = "Remove workspace member",
    dialogDescription = "Are you sure you would like to remove this person from the workspace?",
    dialogSubmitLabel = "Yes, remove it",
}: {
    workspace: Workspace;
    user: UserMembership | User;
    dialogTitle?: string;
    dialogDescription?: string;
    dialogSubmitLabel?: string;
}) {
    const [action, setAction] = useQueryState("action", parseAsStringEnum<Action>(Object.values(Action)));
    const form = useForm({});

    function onSubmit(e: React.FormEvent) {
        e.preventDefault();

        form.delete(route("workspace-members.destroy", [workspace.id, user.id]), {
            errorBag: "removeWorkspaceMember",
            preserveScroll: true,
            preserveState: true,
            onSuccess: async () => {
                form.reset();
                await setAction(null);
            },
        });
    }

    return (
        <Dialog
            onClose={() => setAction(null)}
            open={action === Action.WorkspaceMembersDestroy.replace("{id}", user.id.toString())}
        >
            <DialogHeader>
                <DialogIcon>
                    <EraserIcon className="size-6 text-(--icon-sub-600)" />
                </DialogIcon>

                <div className="flex flex-1 flex-col gap-1">
                    <DialogTitle>{dialogTitle}</DialogTitle>
                    <DialogDescription>{dialogDescription}</DialogDescription>
                </div>
            </DialogHeader>

            <DialogBody>
                <form className="sr-only" id={`destroy-workspace-members-${user.id}-form`} onSubmit={onSubmit} />
            </DialogBody>

            <DialogActions>
                <Button
                    $color="neutral"
                    $size="sm"
                    $variant="stroke"
                    className="w-full"
                    disabled={form.processing}
                    onClick={() => setAction(null)}
                >
                    Cancel
                </Button>
                <Button
                    $color="error"
                    $size="sm"
                    className="w-full"
                    disabled={form.processing}
                    form={`destroy-workspace-members-${user.id}-form`}
                    type="submit"
                >
                    {form.processing ? "Removing..." : dialogSubmitLabel}
                </Button>
            </DialogActions>
        </Dialog>
    );
}
