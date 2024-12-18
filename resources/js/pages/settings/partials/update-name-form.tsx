import { useForm, usePage } from "@inertiajs/react";
import { useDebounce } from "@uidotdev/usehooks";
import { useCallback, useEffect } from "react";
import { toast } from "sonner";

import { Field, Hint, Label } from "#/components/form/fieldset.tsx";
import { Input } from "#/components/form/input.tsx";
import { FormSection } from "#/components/form-section.tsx";
import { type InertiaSharedProps } from "#/types/index.ts";

export function UpdateNameForm() {
    const page = usePage<InertiaSharedProps>();
    const user = page.props.auth.user;
    const { data, setData, errors, ...form } = useForm({
        _method: "PUT",
        name: user?.name,
        email: user?.email,
    });
    const debouncedName = useDebounce(data.name, 1000);

    const sendRequest = useCallback(() => {
        form.post(route("user-profile-information.update"), {
            errorBag: "updateProfileInformation",
            preserveScroll: true,
            onSuccess: () => {
                toast("Profile updated.");
            },
        });
    }, [form]);

    useEffect(() => {
        if (user?.name === debouncedName) return;

        sendRequest();
    }, [debouncedName, sendRequest, user?.name]);

    function onSubmit(e: React.FormEvent) {
        e.preventDefault();

        sendRequest();
    }

    return (
        <FormSection description="Use your real name to build trust with others." title="Full name">
            <form className="flex w-full flex-col gap-4" onSubmit={onSubmit}>
                <Field>
                    <Label className="sr-only">Full name</Label>
                    <Input
                        autoComplete="name"
                        disabled={form.processing}
                        invalid={!!errors.name}
                        name="name"
                        onChange={(e) => setData("name", e.target.value)}
                        placeholder="e.g. John Doe"
                        type="text"
                        value={data.name}
                    />
                    {errors.name && <Hint invalid>{errors.name}</Hint>}
                </Field>
            </form>
        </FormSection>
    );
}
