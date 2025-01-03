import { useForm } from "@inertiajs/react";
import { parseAsStringEnum, useQueryState } from "nuqs";
import TeamIcon from "virtual:icons/ri/team-line";
import { route } from "ziggy-js";

import { Select } from "#/components/select.tsx";
import { TextField } from "#/components/text-field.tsx";
import * as Button from "#/components/ui/button.tsx";
import * as Modal from "#/components/ui/modal.tsx";
import { type Role, type Workspace } from "#/types/index.ts";
import { Action } from "#/utils/action.ts";

interface AddWorkspaceMemberFormProps {
    workspace: Workspace;
    availableRoles: Role[];
}

export function AddWorkspaceMemberForm({ workspace, availableRoles }: AddWorkspaceMemberFormProps) {
    const [action, setAction] = useQueryState("action", parseAsStringEnum<Action>(Object.values(Action)));
    const form = useForm({
        email: "",
        role: availableRoles[0]?.name,
    });

    function onSubmit(e: React.FormEvent) {
        e.preventDefault();

        form.post(route("workspace-members.store", [workspace.id]), {
            errorBag: "addWorkspaceMember",
            preserveScroll: true,
            onSuccess: async () => {
                form.reset();
                await setAction(null);
            },
        });
    }

    return (
        <Modal.Root
            onOpenChange={(open) => setAction(open ? Action.WorkspaceMembersCreate : null)}
            open={action === Action.WorkspaceMembersCreate}
        >
            <Modal.Trigger asChild>
                <Button.Root $size="sm" className="px-4" onClick={() => setAction(Action.WorkspaceMembersCreate)}>
                    Add workspace member
                </Button.Root>
            </Modal.Trigger>

            <Modal.Content className="max-w-[440px]">
                <Modal.Header
                    description="Add a new team member to your team, allowing them to collaborate with you."
                    icon={TeamIcon}
                    title="Add workspace member"
                />

                <Modal.Body>
                    <form className="flex flex-col gap-3" id="add-workspace-member-form" onSubmit={onSubmit}>
                        <TextField
                            $error={!!form.errors.email}
                            autoComplete="off"
                            autoFocus
                            hint={form.errors.email}
                            label="Email address"
                            name="email"
                            onChange={(e) => form.setData("email", e.target.value)}
                            placeholder="i.e. john@example.com"
                            type="email"
                            value={form.data.email}
                        />

                        {availableRoles.length > 0 ? (
                            <Select
                                error={form.errors.role}
                                id="role"
                                label="Role"
                                name="role"
                                onValueChange={(value) => form.setData("role", value)}
                                options={availableRoles.map((role) => ({
                                    value: role.name,
                                    label: role.name,
                                }))}
                                placeholder="Select a role..."
                                position="item-aligned"
                                value={form.data.role}
                            />
                        ) : null}
                    </form>
                </Modal.Body>

                <Modal.Footer>
                    <Modal.Close asChild>
                        <Button.Root
                            $size="sm"
                            $style="stroke"
                            $type="neutral"
                            className="w-full"
                            disabled={form.processing}
                            onClick={() => setAction(null)}
                        >
                            Cancel
                        </Button.Root>
                    </Modal.Close>
                    <Button.Root
                        $size="sm"
                        className="w-full"
                        disabled={form.processing}
                        form="add-workspace-member-form"
                        type="submit"
                    >
                        {form.processing ? "Sending..." : "Send invitation"}
                    </Button.Root>
                </Modal.Footer>
            </Modal.Content>
        </Modal.Root>
    );
}
