import { useForm } from "@inertiajs/react";
import Delete02Icon from "virtual:icons/hugeicons/delete-02";

import { Button } from "#/components/button.tsx";
import * as Modal from "#/components/ui/modal.tsx";
import { useActionsParams } from "#/hooks/use-actions-params.ts";
import { routes } from "#/routes.ts";
import { SubmitButton } from "../submit-button.tsx";

export function DeleteCategoryModal() {
    const actions = useActionsParams();
    const form = useForm();

    const isOpen = actions.action === "delete" && actions.resource === "categories" && !!actions.resourceId;
    const resourceId = actions.resourceId;

    const handleSubmit = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();

        if (!resourceId) {
            return;
        }

        form.delete(
            routes.categories.destroy.url({
                category: resourceId,
            }),
            {
                preserveScroll: true,
                async onSuccess() {
                    await actions.resetParams();
                },
                onError() {
                    form.reset();
                },
            },
        );
    };

    const handleClose = async () => {
        await actions.resetParams();
    };

    return (
        <Modal.Root onOpenChange={handleClose} open={isOpen}>
            <Modal.Content className="max-w-lg">
                <Modal.Header
                    description="This action cannot be undone."
                    icon={Delete02Icon}
                    title="Are you sure you want to delete this category?"
                />

                <Modal.Body className="p-0">
                    <form
                        action={
                            resourceId
                                ? routes.categories.destroy.url({
                                      category: resourceId,
                                  })
                                : undefined
                        }
                        id="delete-category-form"
                        method="POST"
                        onSubmit={handleSubmit}
                    >
                        <input name="_method" type="hidden" value="DELETE" />
                    </form>
                </Modal.Body>

                <Modal.Footer className="border-t-0">
                    <Modal.Close asChild>
                        <Button $size="sm" $style="stroke" $type="neutral" className="w-full" onClick={handleClose}>
                            Cancel
                        </Button>
                    </Modal.Close>
                    <SubmitButton
                        $size="sm"
                        $type="error"
                        className="w-full"
                        form="delete-category-form"
                        isSubmitting={form.processing}
                    >
                        Yes, delete
                    </SubmitButton>
                </Modal.Footer>
            </Modal.Content>
        </Modal.Root>
    );
}
