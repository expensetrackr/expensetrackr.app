import { useForm } from "@inertiajs/react";
import * as React from "react";
import { toast } from "sonner";

import { FormSection } from "#/components/form-section.tsx";
import { useUnsavedChanges } from "#/hooks/use-unsaved-changes.ts";
import { routes } from "#/routes.ts";
import { TextField } from "../ui/form/text-field.tsx";

interface UpdateWorkspaceNameFormProps {
    defaultValues: Pick<App.Data.Workspace.WorkspaceData, "id" | "name">;
    permissions: App.Data.Workspace.WorkspacePermissionsData;
}

/**
 * Renders a form for updating the name of a workspace.
 *
 * Displays a single text field for the workspace name, handles form submission, and provides user feedback on success or failure. The form is disabled if the user lacks permission to update the workspace.
 *
 * @param defaultValues - Initial values for the workspace's ID and name.
 * @param permissions - User permissions related to workspace updates.
 */
export function UpdateWorkspaceNameForm({ defaultValues, permissions }: UpdateWorkspaceNameFormProps) {
    const form = useForm(defaultValues);
    const { dismissUnsavedChanges } = useUnsavedChanges({
        form,
        formId: "update-workspace-name-form",
    });

    const onSubmit = (e: React.FormEvent) => {
        e.preventDefault();

        if (!permissions.canUpdateWorkspace) return;

        form.put(routes.workspaces.update.url({ workspace: defaultValues.id }), {
            errorBag: "updateWorkspace",
            preserveScroll: true,
            onSuccess: () => {
                toast.success("Workspace name updated.", {
                    id: "workspace-name-update-success",
                });
                dismissUnsavedChanges();
            },
            onError: () => {
                toast.error("Failed to update workspace name.", {
                    id: "workspace-name-update-error",
                });
                form.reset();
            },
        });
    };

    return (
        <FormSection description="Your workspace name is how others will recognize you on the platform." title="Name">
            <form
                {...routes.workspaces.update.form({ workspace: defaultValues.id })}
                className="w-full"
                id="update-workspace-name-form"
                onSubmit={onSubmit}
            >
                <TextField
                    autoComplete="off"
                    disabled={!permissions.canUpdateWorkspace || form.processing}
                    error={form.errors.name}
                    id="name"
                    label="Workspace name"
                    labelClassName="sr-only"
                    name="name"
                    onChange={(e) => form.setData("name", e.target.value)}
                    placeholder="e.g. Apple"
                    value={form.data.name}
                />
            </form>
        </FormSection>
    );
}
