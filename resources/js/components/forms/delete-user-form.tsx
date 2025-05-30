import { useForm } from "@inertiajs/react";
import { parseAsStringEnum, useQueryState } from "nuqs";
import * as React from "react";
import UserMinus02SolidIcon from "virtual:icons/hugeicons/user-minus-02-solid";

import { ActionSection } from "#/components/action-section.tsx";
import * as Button from "#/components/ui/button.tsx";
import { TextField } from "#/components/ui/form/text-field.tsx";
import * as Modal from "#/components/ui/modal.tsx";
import { routes } from "#/routes.ts";
import { Action } from "#/utils/action.ts";

export function DeleteUserForm() {
    const [action, setAction] = useQueryState("action", parseAsStringEnum<Action>(Object.values(Action)));
    const form = useForm({
        password: "",
    });
    const passwordRef = React.useRef<HTMLInputElement>(null);

    function onSubmit(e: React.FormEvent) {
        e.preventDefault();

        form.delete(routes.currentUser.destroy.url(), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
            onError: () => passwordRef.current?.focus(),
            onFinish: () => form.reset(),
        });
    }

    async function closeModal() {
        await setAction(null);

        form.reset();
    }

    return (
        <ActionSection
            action={
                <Modal.Root
                    onOpenChange={(open) => setAction(open ? Action.CurrentUserDestroy : null)}
                    open={action === Action.CurrentUserDestroy}
                >
                    <Modal.Trigger asChild>
                        <Button.Root $style="stroke" $type="error" onClick={() => setAction(Action.CurrentUserDestroy)}>
                            Delete account
                        </Button.Root>
                    </Modal.Trigger>

                    <Modal.Content className="max-w-[440px]">
                        <Modal.Header
                            description="Once your account is deleted, all of its resources and data will be permanently deleted."
                            icon={UserMinus02SolidIcon}
                            title="Delete account"
                        />

                        <Modal.Body>
                            <form
                                {...routes.currentUser.destroy.form()}
                                className="flex flex-col gap-3"
                                id="delete-user-form"
                                onSubmit={onSubmit}
                            >
                                <TextField
                                    $error={!!form.errors.password}
                                    autoComplete="current-password"
                                    autoFocus
                                    disabled={form.processing}
                                    hint={form.errors.password}
                                    label="Password"
                                    name="password"
                                    onChange={(e) => form.setData("password", e.target.value)}
                                    placeholder="Enter your password"
                                    type="password"
                                    value={form.data.password}
                                />
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
                                    onClick={closeModal}
                                >
                                    Cancel
                                </Button.Root>
                            </Modal.Close>
                            <Button.Root
                                $size="sm"
                                $type="error"
                                className="w-full"
                                disabled={form.processing}
                                form="delete-user-form"
                                type="submit"
                            >
                                {form.processing ? "Deleting..." : "Yes, delete my account"}
                            </Button.Root>
                        </Modal.Footer>
                    </Modal.Content>
                </Modal.Root>
            }
            description="Permanently delete your account."
            title="Delete account"
        />
    );
}
