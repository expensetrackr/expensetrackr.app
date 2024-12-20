import { Head, useForm } from "@inertiajs/react";
import { route } from "ziggy-js";

import { TextField } from "#/components/text-field.tsx";
import * as FancyButton from "#/components/ui/fancy-button.tsx";
import { AuthLayout } from "#/layouts/auth-layout.tsx";

export default function ConfirmPassword() {
    const { data, setData, post, processing, errors, reset } = useForm({
        password: "",
    });

    const submit: React.FormEventHandler = (e) => {
        e.preventDefault();

        post(route("password.confirm"), {
            onSuccess() {
                reset("password");
            },
        });
    };

    return (
        <AuthLayout description="This is a secure area of the application. Please confirm your password before continuing.">
            <Head title="Confirm Password" />

            <form className="flex flex-col gap-3" onSubmit={submit}>
                <TextField
                    $error={!!errors.password}
                    autoComplete="current-password"
                    autoFocus
                    hint={errors.password}
                    label="Password"
                    name="password"
                    onChange={(e) => setData("password", e.target.value)}
                    placeholder="Enter your password"
                    type="password"
                    value={data.password}
                />

                <FancyButton.Root disabled={processing} type="submit">
                    Confirm
                </FancyButton.Root>
            </form>
        </AuthLayout>
    );
}
